<?php
/**
 * Plugin Name:       Additional function of displaying specific information
 * Plugin URI:        https://fonts.google.com
 * Description:       Demo version
 * Version:           1.0.0
 * Author:            QuangT
 * Author URI:        https://fonts.google.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       DEMO
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
    die;
}

define('DEMO_VERSION', '1.0.0');
!defined('DEMO_PATH') && define('DEMO_PATH', plugin_dir_path(__FILE__));

// Plugin activation and deactivation hooks
register_activation_hook(__FILE__, function () {
    require_once DEMO_PATH . 'includes/class-demo-activator.php';
    Demo_Activator::activate();
});

register_deactivation_hook(__FILE__, function () {
    require_once DEMO_PATH . 'includes/class-demo-deactivator.php';
    Demo_Deactivator::deactivate();
});

require DEMO_PATH . 'includes/class-demo.php';

// Initialize the plugin
(new Demo())->run();

// Shortcode function to display custom content
add_shortcode('display_text', function () {
    // Lấy các giá trị cấu hình từ tùy chọn
    $userID = get_option("Demo_setting_number1") ?: 1232;
    $url = "https://trealet.com/api/my-trealets/$userID";

    $is_dark_mode = get_option("Demo_setting_bool1") === "true";
    $list_type_option = get_option("Demo_setting_bool") === "true";

    $border_radius = get_option("Demo_setting_border_radius");
    $background_color = get_option("Demo_setting_bg_code");

    // Xác định các class CSS dựa trên chế độ tối/sáng
    $theme_background = $is_dark_mode ? "dark-theme-background" : "light-theme-background";
    $theme_item = $is_dark_mode ? "item-dark-theme" : "";
    $white_color = $is_dark_mode ? "name-color-darkmode" : "";
    $article_count_on_page = 5;

    // Lấy dữ liệu từ API
    $data = get_data($url);

    // Kiểm tra dữ liệu
    if (!$data) {
        return "<h3>Có vấn đề trong việc xử lý dữ liệu, hãy thử lại sau!</h3>";
    }

    // Chèn thư viện icon
    apply_icon_library();

    // Tạo cấu trúc HTML chính
    $html = "
        <section class='full' style='background-color: $background_color;'>
            <div class='cover'>
                <div class='navigation'>
                    " . render_header($white_color) . "
                    <div class='direct'>
                        <a href='https://trealet.com/'><ion-icon name='help-circle-outline'></ion-icon></a>
                        <div class='user-avt'></div>
                    </div>
                </div>

                <div class='bot-cover {$theme_background}'>
                    <div class='option-section {$theme_background}'>
                        " . render_search_bar() . "
                        " . render_article_list($data, $list_type_option, $article_count_on_page, $is_dark_mode) . "
                    </div>
                    <div class='option_content {$theme_item}'>
                        " . render_articles($data) . "
                    </div>
                </div>
            </div>
        </section>
    ";

    // Xuất HTML
    echo $html;
});


// Helper functions

function render_header($white_color)
{
    return "
        <div class='header {$white_color}'>
            <div class='trealet-logo-image'>
                <img src='https://trealet.com/assets/img/icons/apple-touch-icon-144x144.png' class=''>
            </div>

            <div class='trealet-logo'>
                <h1>Trealet</h1>
                <p>Knowledge of Art & Culture</p>
            </div>
        </div>";

            //<div class='sidebar-close-icon sidebar-icon'>
            //     <ion-icon name='chevron-back-outline'></ion-icon>
            // </div>

            // <div class='sidebar-open-icon sidebar-icon'>
            //     <ion-icon name='chevron-forward-outline'></ion-icon>
            // </div>
}

function render_search_bar()
{
    return "
        <div class='search_bar'>
            <input class='search-input' placeholder='Search for article ...' />
            <div class='search-icon'><ion-icon name='search-outline'></ion-icon></div>
        </div>";
}

function render_articles($data)
{
    $output = "";
    foreach ($data as $key => $value) {
        $jsonObject = json_decode($value['json'], true);

        if (is_array($jsonObject)) {
            $trealet = $jsonObject['trealet'];
            $items = $trealet['items'];
            $output .= "<h2 class='content-title-name' data-id='$key'>{$value['title']}</h2>";

            foreach ($items as $elements) {
                foreach ($elements as $mediaType => $mediaData) {
                    $output .= render_media_unit($key, $mediaType, $mediaData);
                }
            }
        }
    }
    return $output;
}

function render_media_unit($key, $mediaType, $mediaData)
{
    $output = "<div class='content_unit' data-id='$key'>";

    $title = $mediaData[$mediaType . '_title'] ?? "(User didn't add title yet)";
    $description = $mediaData['description_' . $mediaType] ?? '';

    $output .= "<div class='content_unit_title'>
                    <h3>{$title}</h3>
                    <p>{$description}</p>
                </div>";

    switch ($mediaType) {
        case 'video':
            $src = "https://trealet.com" . substr($mediaData['video_src'], 2);
            $output .= "<div><video width='250' height='250' controls muted>
                            <source src='{$src}' type='video/mp4'>Video not found
                        </video></div>";
            break;
        case 'picture':
            $src = "https://trealet.com" . substr($mediaData['picture_src'], 2);
            $output .= "<img src='{$src}' class='picture'>";
            break;
        case 'audio':
            $src = "https://trealet.com" . substr($mediaData['audio_src'], 2);
            $output .= "<audio controls>
                            <source src='{$src}' class='audio'>
                        </audio>";
            break;
    }

    return $output . "</div>";
}

function render_article_list($data, $list_type_option, $article_count_on_page, $is_dark_mode)
{
    $background = $is_dark_mode ? "dark-scrollbar" : "light-scrollbar";
    $output = $list_type_option
        ? "<ul class='article_list_scroll article_list {$background}'>"
        : "<ul class='article_list_page article_list {$background}'>";

    foreach ($data as $key => $value) {
        $number = str_pad(count($data) - $key, 2, '0', STR_PAD_LEFT);
        // $output .= "<li class='title' data-id='$key'><span>{$number}</span><p>{$value['title']}</p></li>";
        $output .= "<li class='title' data-id='$key'><p>{$value['title']}</p></li>";

    }

    $output .= "</ul>";
    if (!$list_type_option) {
        $output .= render_pagination(count($data), $article_count_on_page);
    }
    return $output;
}

function render_pagination($total_articles, $articles_per_page)
{
    $total_pages = ceil($total_articles / $articles_per_page);
    return "
        <ul class='page_option'>
            <li class='pre'><ion-icon name='chevron-back-outline'></ion-icon></li>
            <li class='pos1'>1</li>
            <li class='pos2'>2</li>
            <li class='pos3'>3</li>
            <li class='next'><ion-icon name='chevron-forward-outline'></ion-icon></li>
        </ul>";
}

function get_data($url)
{
    $response = wp_remote_get($url);
    if (!is_wp_error($response)) {
        return json_decode(wp_remote_retrieve_body($response), true);
    }
    echo "<div>ERROR: Unable to fetch data.</div>";
    return null;
}

function apply_icon_library()
{
    echo "
        <script type='module' src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js'></script>
        <script nomodule src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js'></script>";
}
?>