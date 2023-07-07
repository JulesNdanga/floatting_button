<?php
/*
Plugin Name: Floating Button Sendinblue
Description: Ajoute un bouton flottant qui ouvre un formulaire Sendinblue.
Version: 1.0
Author: GOLIAT WEB
Author URI: https://goliat.biz
*/

// Fonction pour vérifier si la page actuelle correspond à la liste des pages spécifiques
function is_allowed_page() {
    $allowed_pages = get_option('floating_button_sendinblue_pages'); // Récupère les pages autorisées depuis les options
    $allowed_pages_2 = get_option('floating_button_sendinblue_pages_2'); // Récupère les pages autorisées 2 depuis les options

    // Vérifie si la page actuelle est la page d'accueil
    if (is_front_page() && (strpos($allowed_pages, 'home') !== false || strpos($allowed_pages_2, 'home') !== false)) {
        return true;
    }

    // Récupère les slugs de page autorisés
    $allowed_slugs = array_merge(explode(',', $allowed_pages), explode(',', $allowed_pages_2));

    // Vérifie si le slug de la page actuelle correspond à l'un des slugs de page autorisés
    $current_page_slug = get_post()->post_name;
    if (in_array($current_page_slug, $allowed_slugs)) {
        return true;
    }

    return false;
}

function add_floating_button() {
    if (!is_allowed_page()) {
        return;
    }

    $options = get_option('floating_button_sendinblue_options');
    $button_text = isset($options['button_text']) ? $options['button_text'] : 'Demande de devis';
    $button_css = isset($options['button_css']) ? $options['button_css'] : '';

    $shortcode_id = get_option('floating_button_sendinblue_shortcode_id'); // Récupère l'ID du shortcode spécifique à la page
    $shortcode = $shortcode_id ? get_option('floating_button_sendinblue_shortcode_' . $shortcode_id) : ''; // Récupère le shortcode spécifique à la page

    $shortcode_id_2 = get_option('floating_button_sendinblue_shortcode_id_2'); // Récupère l'ID du shortcode spécifique à la page 2
    $shortcode_2 = $shortcode_id_2 ? get_option('floating_button_sendinblue_shortcode_' . $shortcode_id_2) : ''; // Récupère le shortcode spécifique à la page 2

    echo '
    <button id="floating-button" style="' . $button_css . '">' . $button_text . '</button>
    
    <div id="modalOverlay"></div>
    <div id="sendinblue-modal">
        <button id="closeModalBtn">&times;</button>
        <h6 class="titre-formulaire">' . esc_html(get_option('floating_button_sendinblue_form_title', 'Recevez Votre Tarif Express En Moins de 02 min')) . '</h6>';

    if (is_allowed_page()) {
        if (is_allowed_page_2()) {
            echo do_shortcode("[sibwp_form id=19]");
        } else {
            echo do_shortcode($shortcode);
        }
    }

    echo '
    </div>
    ';

    echo '<style>';
    $custom_css = isset($options['custom_css']) ? $options['custom_css'] : '';
    echo $custom_css;
    echo '</style>';

    echo '<script>
        const button = document.getElementById("floating-button");
        const modalOverlay = document.getElementById("modalOverlay");
        const modal = document.getElementById("sendinblue-modal");
        const closeModalBtn = document.getElementById("closeModalBtn");

        button.addEventListener("click", function () {
            modalOverlay.style.display = "block";
            modal.style.display = "block";
            document.body.style.overflow = "hidden";
        });

        closeModalBtn.addEventListener("click", function () {
            modalOverlay.style.display = "none";
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        });

        window.addEventListener("scroll", function () {
            const windowHeight = window.innerHeight;
            const scrollHeight = window.pageYOffset;

            // Modifier la valeur (200) ci-dessous selon votre besoin
            if (scrollHeight > windowHeight - 200) {
                button.classList.add("scrolled");
            } else {
                button.classList.remove("scrolled");
            }
        });
    </script>';
}

// Fonction pour vérifier si la page actuelle correspond à la liste des pages spécifiques 2
function is_allowed_page_2() {
    $allowed_pages_2 = get_option('floating_button_sendinblue_pages_2'); // Récupère les pages autorisées 2 depuis les options

    // Vérifie si la page actuelle est la page d'accueil
    if (is_front_page() && strpos($allowed_pages_2, 'home') !== false) {
        return true;
    }

    // Récupère les slugs de page autorisés
    $allowed_slugs_2 = explode(',', $allowed_pages_2);

    // Vérifie si le slug de la page actuelle correspond à l'un des slugs de page autorisés
    $current_page_slug = get_post()->post_name;
    if (in_array($current_page_slug, $allowed_slugs_2)) {
        return true;
    }

    return false;
}

// Fonction pour afficher la page de configuration
function floating_button_sendinblue_config_page() {
    $options = get_option('floating_button_sendinblue_options');
    $button_text = isset($options['button_text']) ? $options['button_text'] : 'DECOUVRIR NOS TARIFS';
    $button_css = isset($options['button_css']) ? $options['button_css'] : '';
    $custom_css = isset($options['custom_css']) ? $options['custom_css'] : '';

    $shortcode_id = get_option('floating_button_sendinblue_shortcode_id'); // Récupère l'ID du shortcode spécifique à la page
    $shortcode = $shortcode_id ? get_option('floating_button_sendinblue_shortcode_' . $shortcode_id) : ''; // Récupère le shortcode spécifique à la page

    $shortcode_id_2 = get_option('floating_button_sendinblue_shortcode_id_2'); // Récupère l'ID du shortcode spécifique à la page 2
    $shortcode_2 = $shortcode_id_2 ? get_option('floating_button_sendinblue_shortcode_' . $shortcode_id_2) : ''; // Récupère le shortcode spécifique à la page 2

    // Récupère les pages autorisées
    $allowed_pages = get_option('floating_button_sendinblue_pages');
    $allowed_pages_2 = get_option('floating_button_sendinblue_pages_2'); // Nouveau champ "Pages autorisées 2"

    echo '
    <div class="wrap">
        <h1>Floating Button Sendinblue Configuration</h1>
        <form method="post" action="options.php">
            ' . wp_nonce_field('update-options') . '
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Texte du bouton</th>
                    <td><input type="text" name="floating_button_sendinblue_options[button_text]" value="' . esc_attr($button_text) . '" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Titre du formulaire</th>
                    <td><input type="text" name="floating_button_sendinblue_form_title" value="' . esc_attr(get_option('floating_button_sendinblue_form_title', 'Recevez Votre Tarif Express En Moins de 02 min')) . '" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">CSS personnalisé</th>
                    <td><textarea name="floating_button_sendinblue_options[custom_css]" rows="5" cols="50">' . esc_textarea($custom_css) . '</textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Pages autorisées</th>
                    <td><input type="text" name="floating_button_sendinblue_pages" value="' . esc_attr($allowed_pages) . '" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Shortcode de la page</th>
                    <td><input type="text" name="floating_button_sendinblue_shortcode" value="' . esc_attr($shortcode) . '" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Pages autorisées 2</th>
                    <td><input type="text" name="floating_button_sendinblue_pages_2" value="' . esc_attr($allowed_pages_2) . '" /></td>
                </tr>
                <tr valign="top">
					<th scope="row">Shortcode de la page 2</th>
					<td><input type="text" name="floating_button_sendinblue_shortcode_2" value="' . esc_attr($shortcode_2) . '" /></td>
				</tr>
            </table>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="floating_button_sendinblue_options,floating_button_sendinblue_pages,floating_button_sendinblue_shortcode,floating_button_sendinblue_pages_2,floating_button_sendinblue_shortcode_2" />
            <p class="submit">
                <input type="submit" class="button-primary" value="Enregistrer les modifications" />
            </p>
        </form>
    </div>
    ';
}

// Fonction pour enregistrer les options de configuration
function floating_button_sendinblue_save_config() {
    if (isset($_POST['floating_button_sendinblue_options'])) {
        $options = $_POST['floating_button_sendinblue_options'];
        update_option('floating_button_sendinblue_options', $options);
    }

    if (isset($_POST['floating_button_sendinblue_pages'])) {
        $allowed_pages = sanitize_text_field($_POST['floating_button_sendinblue_pages']);
        update_option('floating_button_sendinblue_pages', $allowed_pages);
    }

    if (isset($_POST['floating_button_sendinblue_shortcode'])) {
        $shortcode = sanitize_text_field($_POST['floating_button_sendinblue_shortcode']);
        $shortcode_id = get_option('floating_button_sendinblue_shortcode_id');
        update_option('floating_button_sendinblue_shortcode_' . $shortcode_id, $shortcode);
    }

    if (isset($_POST['floating_button_sendinblue_pages_2'])) {
        $allowed_pages_2 = sanitize_text_field($_POST['floating_button_sendinblue_pages_2']);
        update_option('floating_button_sendinblue_pages_2', $allowed_pages_2);
    }

    if (isset($_POST['floating_button_sendinblue_shortcode_2'])) {
		$shortcode_2 = sanitize_text_field($_POST['floating_button_sendinblue_shortcode_2']);
        $shortcode_id_2 = get_option('floating_button_sendinblue_shortcode_id_2');
        update_option('floating_button_sendinblue_shortcode_' . $shortcode_id_2, $shortcode_2);
    }

    if (isset($_POST['floating_button_sendinblue_form_title'])) {
        $form_title = sanitize_text_field($_POST['floating_button_sendinblue_form_title']);
        update_option('floating_button_sendinblue_form_title', $form_title);
    }

    wp_redirect(admin_url('options-general.php?page=floating-button-sendinblue'));
    exit;
}

// Enregistre les options de configuration lors de la soumission du formulaire
add_action('admin_post_floating_button_sendinblue_save_config', 'floating_button_sendinblue_save_config');

// Ajoute le bouton flottant sur les pages autorisées
add_action('wp_footer', 'add_floating_button');

// Ajoute la page de configuration au menu principal de WordPress
function floating_button_sendinblue_add_config_page() {
    add_menu_page(
        'Floating Button Sendinblue Configuration',
        'Floating Button Sendinblue',
        'manage_options',
        'floating-button-sendinblue',
        'floating_button_sendinblue_config_page',
        'dashicons-email',
        50
    );
}
add_action('admin_menu', 'floating_button_sendinblue_add_config_page');
?>