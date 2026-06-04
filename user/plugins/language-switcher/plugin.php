<?php
/*
Plugin Name: Language Switcher
Plugin URI: https://github.com/gioxx/YOURLS-DockerCustom
Description: Lets an admin choose the active YOURLS language from the plugins page.
Version: 1.0.0
Author: Gioxx
Author URI: https://gioxx.org/
*/

if ( !defined( 'YOURLS_ABSPATH' ) ) {
    die();
}

define( 'YOURLS_LANGUAGE_SWITCHER_OPTION', 'yourls_language_switcher_locale' );
define( 'YOURLS_LANGUAGE_SWITCHER_PAGE', 'language-switcher' );
define( 'YOURLS_LANGUAGE_SWITCHER_ACTION', 'language_switcher_settings' );

yourls_add_filter( 'get_locale', 'yourls_language_switcher_get_locale' );
yourls_add_action( 'plugins_loaded', 'yourls_language_switcher_register_page' );

function yourls_language_switcher_get_locale( $locale ) {
    $selected = yourls_language_switcher_selected_locale();

    if ( $selected === '' ) {
        return $locale;
    }

    return $selected;
}

function yourls_language_switcher_register_page() {
    yourls_register_plugin_page(
        YOURLS_LANGUAGE_SWITCHER_PAGE,
        'Language Switcher',
        'yourls_language_switcher_render_page'
    );
}

function yourls_language_switcher_render_page() {
    $message = '';

    if ( isset( $_POST['language_locale'] ) ) {
        yourls_verify_nonce( YOURLS_LANGUAGE_SWITCHER_ACTION );
        $message = yourls_language_switcher_handle_save();
    }

    $available_locales = yourls_language_switcher_available_locales();
    $current_locale = yourls_language_switcher_selected_locale();
    $nonce = yourls_create_nonce( YOURLS_LANGUAGE_SWITCHER_ACTION );
    $current_label = yourls_language_switcher_locale_label( $current_locale );

    echo '<div class="wrap">';
    echo '<h2>Language Switcher</h2>';

    if ( $message ) {
        echo '<p><strong>' . yourls_esc_html( $message ) . '</strong></p>';
    }

    echo '<p>Choose the YOURLS admin language without editing `config.php`.</p>';
    echo '<p><strong>Current language:</strong> ' . yourls_esc_html( $current_label ) . '</p>';
    echo '<form method="post">';
    echo '<input type="hidden" name="nonce" value="' . yourls_esc_attr( $nonce ) . '" />';
    echo '<p><label for="language_locale">Language</label><br />';
    echo '<select id="language_locale" name="language_locale">';
    echo '<option value="">' . yourls_esc_html( 'English (default)' ) . '</option>';

    foreach ( $available_locales as $locale ) {
        $selected = ( $locale === $current_locale ) ? ' selected="selected"' : '';
        $label = yourls_language_switcher_locale_label( $locale );
        echo '<option value="' . yourls_esc_attr( $locale ) . '"' . $selected . '>' . yourls_esc_html( $label ) . '</option>';
    }

    echo '</select></p>';
    echo '<p><input type="submit" class="button button-primary" value="' . yourls_esc_attr( 'Save' ) . '" /></p>';
    echo '</form>';
    echo '</div>';
}

function yourls_language_switcher_handle_save() {
    $selected = isset( $_POST['language_locale'] ) ? trim( (string) $_POST['language_locale'] ) : '';
    $available_locales = yourls_language_switcher_available_locales();

    if ( $selected === '' ) {
        yourls_delete_option( YOURLS_LANGUAGE_SWITCHER_OPTION );
        return 'Language reset to English (default).';
    }

    if ( !in_array( $selected, $available_locales, true ) ) {
        return 'Selected language is not available.';
    }

    yourls_update_option( YOURLS_LANGUAGE_SWITCHER_OPTION, $selected );
    return 'Language updated successfully.';
}

function yourls_language_switcher_selected_locale() {
    $selected = yourls_get_option( YOURLS_LANGUAGE_SWITCHER_OPTION, '' );
    $available_locales = yourls_language_switcher_available_locales();

    if ( !is_string( $selected ) || $selected === '' ) {
        return '';
    }

    if ( !in_array( $selected, $available_locales, true ) ) {
        return '';
    }

    return $selected;
}

function yourls_language_switcher_available_locales() {
    $locales = yourls_get_available_languages();

    sort( $locales );

    return array_values( array_unique( $locales ) );
}

function yourls_language_switcher_locale_label( $locale ) {
    if ( $locale === '' ) {
        return 'English (default)';
    }

    $labels = array(
        'bg_BG' => 'Bulgarian',
        'ca_ES' => 'Catalan',
        'cs_CZ' => 'Czech',
        'de_DE' => 'German',
        'de_CH' => 'German (Switzerland)',
        'da_DK' => 'Danish',
        'en_AU' => 'English (Australian)',
        'es_ES' => 'Spanish',
        'fa_FA' => 'Farsi',
        'fi_FI' => 'Finnish',
        'fr_FR' => 'French',
        'hi-IN' => 'Hindi',
        'id_ID' => 'Indonesian',
        'it_IT' => 'Italian',
        'ja_JP' => 'Japanese',
        'ko_KR' => 'Korean',
        'nb_NO' => 'Norwegian (bokmal)',
        'nl_NL' => 'Dutch',
        'pl_PL' => 'Polish',
        'pt_BR' => 'Portuguese (Brazil)',
        'pt_PT' => 'Portuguese',
        'ru_RU' => 'Russian',
        'sk_SK' => 'Slovak',
        'te_IN' => 'Telugu',
        'tr_TR' => 'Turkish',
        'uk' => 'Ukrainian',
        'zh_CN' => 'Chinese (Simplified)',
        'zh_TW' => 'Chinese (Traditional)',
    );

    if ( isset( $labels[ $locale ] ) ) {
        return $labels[ $locale ] . ' (' . $locale . ')';
    }

    return $locale;
}
