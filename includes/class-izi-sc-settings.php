<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class IZISMFOY_Settings {
    private $verifier;

    public function __construct( $verifier ) {
        $this->verifier = $verifier;
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'admin_init', array( $this, 'register' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
    }

    public function menu() {
        add_options_page(
            __( '2IZI SmartCaptcha', '2izi-smartcaptcha' ),
            __( '2IZI SmartCaptcha', '2izi-smartcaptcha' ),
            'manage_options',
            '2izi-smartcaptcha',
            array( $this, 'page' )
        );
    }

    public function register() {
        register_setting( 'izismfoy', 'izismfoy_options', array( $this, 'sanitize' ) );
    }

    public function sanitize( $input ) {
        $old = get_option( 'izismfoy_options', array() );
        $out = array();
        $out['client_key'] = isset( $input['client_key'] ) ? sanitize_text_field( $input['client_key'] ) : '';
        $new_server = isset( $input['server_key'] ) ? trim( (string) $input['server_key'] ) : '';
        $out['server_key'] = '' !== $new_server ? sanitize_text_field( $new_server ) : ( isset( $old['server_key'] ) ? $old['server_key'] : '' );
        $allowed_lang = array( '', 'ru', 'en', 'be', 'kk', 'tt', 'uk', 'uz', 'tr' );
        $out['language'] = ( isset( $input['language'] ) && in_array( $input['language'], $allowed_lang, true ) ) ? $input['language'] : '';
        foreach ( array( 'core_login','core_register','core_lostpassword','core_comments','woo_login','woo_register','woo_lostpassword','woo_checkout','woo_review','cf7','fail_closed' ) as $key ) {
            $out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
        }
        add_settings_error( 'izismfoy_messages', 'izismfoy_saved', __( 'Settings saved.', '2izi-smartcaptcha' ), 'updated' );
        return $out;
    }

    public function assets( $hook ) {
        if ( 'settings_page_2izi-smartcaptcha' !== $hook ) { return; }
        wp_enqueue_style( 'izismfoy-admin', IZISMFOY_URL . 'assets/admin.css', array(), IZISMFOY_VERSION );
        wp_enqueue_script( 'izismfoy-admin', IZISMFOY_URL . 'assets/admin.js', array(), IZISMFOY_VERSION, true );

        $o = $this->verifier->get_options();
        wp_localize_script( 'izismfoy-admin', 'izismfoyAdmin', array(
            'configured' => $this->verifier->is_configured(),
            'siteKey'    => isset( $o['client_key'] ) ? $o['client_key'] : '',
            'language'   => IZISMFOY_Language::resolve( isset( $o['language'] ) ? $o['language'] : '' ),
        ) );

        if ( $this->verifier->is_configured() ) {
            wp_enqueue_script(
                'izismfoy-admin-yandex',
                'https://smartcaptcha.cloud.yandex.ru/captcha.js?render=onload&onload=izismfoyAdminCaptchaOnload',
                array( 'izismfoy-admin' ),
                IZISMFOY_VERSION,
                true
            );
        }
    }


    private function checkbox_row( $name, $label, $o, $desc = '' ) {
        ?>
        <label for="izi-sc-<?php echo esc_attr( $name ); ?>">
            <input id="izi-sc-<?php echo esc_attr( $name ); ?>" type="checkbox" name="izismfoy_options[<?php echo esc_attr( $name ); ?>]" value="1" <?php checked( ! empty( $o[ $name ] ) ); ?>>
            <?php echo esc_html( $label ); ?>
        </label>
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo esc_html( $desc ); ?></p>
        <?php endif;
    }

    private function section_heading( $title, $description = '' ) {
        echo '<h2 class="title">' . esc_html( $title ) . '</h2>';
        if ( $description ) {
            echo '<p>' . esc_html( $description ) . '</p>';
        }
    }

    public function page() {
        if ( ! current_user_can( 'manage_options' ) ) { return; }
        $o = $this->verifier->get_options();
        ?>
        <div class="wrap izi-sc-wrap">
            <h1><?php esc_html_e( '2IZI SmartCaptcha for Yandex', '2izi-smartcaptcha' ); ?></h1>
            <p class="description izi-sc-intro"><?php esc_html_e( 'Protect WordPress forms with Yandex SmartCaptcha.', '2izi-smartcaptcha' ); ?> <span class="izi-sc-version">v<?php echo esc_html( IZISMFOY_VERSION ); ?></span></p>

            <?php settings_errors( 'izismfoy_messages' ); ?>

            <?php if ( ! $this->verifier->is_configured() ) : ?>
                <div class="notice notice-warning inline"><p><?php esc_html_e( 'Enter the Client key and Server key from Yandex SmartCaptcha to enable protection.', '2izi-smartcaptcha' ); ?></p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'izismfoy' ); ?>

                <?php $this->section_heading( __( 'Connection', '2izi-smartcaptcha' ), __( 'Keys are created in the Yandex Cloud SmartCaptcha console.', '2izi-smartcaptcha' ) ); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="izi-sc-client-key"><?php esc_html_e( 'Client key', '2izi-smartcaptcha' ); ?></label></th>
                        <td>
                            <input id="izi-sc-client-key" class="regular-text code" type="text" name="izismfoy_options[client_key]" value="<?php echo esc_attr( $o['client_key'] ); ?>" autocomplete="off">
                            <p class="description"><?php esc_html_e( 'Public site key used by the SmartCaptcha widget.', '2izi-smartcaptcha' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="izi-sc-server-key"><?php esc_html_e( 'Server key', '2izi-smartcaptcha' ); ?></label></th>
                        <td>
                            <input id="izi-sc-server-key" class="regular-text code" type="password" name="izismfoy_options[server_key]" value="" placeholder="<?php echo empty( $o['server_key'] ) ? '' : esc_attr__( 'Saved — leave blank to keep', '2izi-smartcaptcha' ); ?>" autocomplete="new-password">
                            <p class="description"><?php esc_html_e( 'Secret key used only for server-side token verification. It is never sent to visitors.', '2izi-smartcaptcha' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="izi-sc-language"><?php esc_html_e( 'SmartCaptcha language', '2izi-smartcaptcha' ); ?></label></th>
                        <td>
                            <select id="izi-sc-language" name="izismfoy_options[language]">
                                <?php
                                $languages = array(
                                    ''   => __( 'Automatic — follow WordPress language', '2izi-smartcaptcha' ),
                                    'ru' => 'Русский',
                                    'en' => 'English',
                                    'be' => 'Беларуская',
                                    'kk' => 'Қазақша',
                                    'tt' => 'Татарча',
                                    'uk' => 'Українська',
                                    'uz' => 'Oʻzbekcha',
                                    'tr' => 'Türkçe',
                                );
                                foreach ( $languages as $value => $label ) {
                                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( $o['language'], $value, false ) . '>' . esc_html( $label ) . '</option>';
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'The plugin interface follows the WordPress admin language. The SmartCaptcha widget itself supports the languages provided by Yandex.', '2izi-smartcaptcha' ); ?></p>
                        </td>
                    </tr>
                    <?php if ( $this->verifier->is_configured() ) : ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'SmartCaptcha', '2izi-smartcaptcha' ); ?></th>
                        <td>
                            <div class="izi-sc-admin-preview">
                                <div id="izi-sc-admin-captcha"></div>
                            </div>
                            <p class="description"><?php esc_html_e( 'The saved Client key is used to load the real Yandex SmartCaptcha widget here.', '2izi-smartcaptcha' ); ?></p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>

                <?php $this->section_heading( __( 'WordPress protection', '2izi-smartcaptcha' ), __( 'Choose which standard WordPress forms require CAPTCHA verification.', '2izi-smartcaptcha' ) ); ?>
                <table class="form-table" role="presentation">
                    <tr><th scope="row"><?php esc_html_e( 'Login', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'core_login', __( 'Protect the login form', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Registration', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'core_register', __( 'Protect the registration form', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Lost password', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'core_lostpassword', __( 'Protect the password reset form', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Comments', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'core_comments', __( 'Protect comments from guests', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                </table>

                <?php $this->section_heading( __( 'WooCommerce protection', '2izi-smartcaptcha' ), __( 'These options are used only when WooCommerce is active.', '2izi-smartcaptcha' ) ); ?>
                <table class="form-table" role="presentation">
                    <tr><th scope="row"><?php esc_html_e( 'Login', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'woo_login', __( 'Protect customer login', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Registration', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'woo_register', __( 'Protect customer registration', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Lost password', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'woo_lostpassword', __( 'Protect customer password reset', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Checkout', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'woo_checkout', __( 'Protect checkout', '2izi-smartcaptcha' ), $o, __( 'Disabled by default. Test carefully with your checkout configuration.', '2izi-smartcaptcha' ) ); ?></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Product reviews', '2izi-smartcaptcha' ); ?></th><td><?php $this->checkbox_row( 'woo_review', __( 'Protect product reviews from guests', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                </table>

                <?php $this->section_heading( __( 'Integrations', '2izi-smartcaptcha' ) ); ?>
                <table class="form-table" role="presentation">
                    <tr><th scope="row">Contact Form 7</th><td><?php $this->checkbox_row( 'cf7', __( 'Enable protection for Contact Form 7 forms', '2izi-smartcaptcha' ), $o ); ?></td></tr>
                </table>

                <?php $this->section_heading( __( 'Security', '2izi-smartcaptcha' ) ); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Verification service failure', '2izi-smartcaptcha' ); ?></th>
                        <td><?php $this->checkbox_row( 'fail_closed', __( 'Block protected submissions if Yandex SmartCaptcha cannot be reached', '2izi-smartcaptcha' ), $o, __( 'Recommended for stronger protection. If disabled, a temporary Yandex service outage will not block protected forms.', '2izi-smartcaptcha' ) ); ?></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <?php
            $project_urls = array(
                'ru' => 'https://2izi.ru/projects/smartcaptcha',
                'en' => 'https://2izi.ru/en/projects/smartcaptcha',
                'ja' => 'https://2izi.ru/ja/projects/smartcaptcha',
                'zh' => 'https://2izi.ru/cn/projects/smartcaptcha',
                'it' => 'https://2izi.ru/it/projects/smartcaptcha',
            );
            $current_locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
            $locale_prefix  = strtolower( substr( (string) $current_locale, 0, 2 ) );
            $project_url    = isset( $project_urls[ $locale_prefix ] ) ? $project_urls[ $locale_prefix ] : $project_urls['en'];
            ?>
            <div class="izi-sc-developer" aria-label="Plugin developer">
                <p><strong>Developed by 2IZI</strong></p>
                <p>2IZI develops secure web solutions, WordPress plugins and integrations.</p>
                <p><a href="<?php echo esc_url( $project_url ); ?>" target="_blank" rel="noopener noreferrer">2IZI SmartCaptcha project</a> &middot; <a href="https://2izi.ru/" target="_blank" rel="noopener noreferrer">2izi.ru</a></p>
            </div>
        </div>
        <?php
    }
}
