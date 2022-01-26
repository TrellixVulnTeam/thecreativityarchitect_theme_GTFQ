source<?php
/**
 * Author: CJMTermini | @deviorobert
 * URL: .com | @
 * Custom functions, support, custom post types and more.
 */

require_once 'inc/modules/is-debug.php';

/*------------------------------------*\
    External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
    Theme Support
\*------------------------------------*/

if ( ! isset( $content_width ) ) {
    $content_width = 1400;
}

if ( function_exists( 'add_theme_support' ) ) {

    // Add Thumbnail Theme Support.
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'large', 700, '', true ); // Large Thumbnail.
    add_image_size( 'medium', 250, '', true ); // Medium Thumbnail.
    add_image_size( 'small', 120, '', true ); // Small Thumbnail.
    add_image_size( 'custom-size', 700, 200, true ); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use.
    add_theme_support('custom-background', array(
    'default-color' => 'FFF',
    'default-image' => get_template_directory_uri() . '/source/img/backgrounds/mobile/9.png'
    ));

    // Add Support for Custom Header - Uncomment below if you're going to use.
    add_theme_support('custom-header', array(
    'default-image'          => get_template_directory_uri() . '/source/img/logo/logo-name-large.png',
    'header-text'            => false,
    'default-text-color'     => '000',
    'width'                  => 1000,
    'height'                 => 198,
    'random-default'         => false
      ));

    // Enables post and comment RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Enable HTML5 support.
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

    // Localisation Support.
    load_theme_textdomain( 'THEMENAME', get_template_directory() . '/inc/languages' );
}

/*------------------------------------*\
    Functions
\*------------------------------------*/

// THEMENAME navigation
function THEMENAME_nav() {
    wp_nav_menu(
    array(
        'theme_location'  => 'header-menu',
        'menu'            => '',
        'container'       => 'div',
        'container_class' => 'menu-{menu slug}-container',
        'container_id'    => '',
        'menu_class'      => 'menu',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<ul>%3$s</ul>',
        'depth'           => 0,
        'walker'          => '',
        )
    );
}

// Load THEMENAME scripts (header.php)
function THEMENAME_header_scripts() {
    if ( $GLOBALS['pagenow'] != 'wp-login.php' && ! is_admin() ) {
        if ( THEMENAME_DEBUG ) {
            // jQuery
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', get_template_directory_uri() . '/source/js/lib/jquery.js', array(), '1.11.1' );

            // Conditionizr
            wp_register_script( 'conditionizr', get_template_directory_uri() . '/source/js/lib/conditionizr-4.3.0.min.js', array(), '4.3.0' );

            // Modernizr
            wp_register_script( 'modernizr', get_template_directory_uri() . '/source/js/lib/modernizr.js', array(), '2.8.3' );

            // Custom scripts
            wp_register_script(
                'THEMENAMEscripts',
                get_template_directory_uri() . '/source/js/scripts.js',
                array(
                    'conditionizr',
                    'modernizr',
                    'jquery'
                ),
                '1.0.0' );

            // Enqueue Scripts
            wp_enqueue_script( 'THEMENAMEscripts' );

        // If production
        } else {
            // Scripts minify
            wp_register_script( 'THEMENAMEscripts-min', get_template_directory_uri() . '/source/js/scripts.min.js', array(), '1.0.0' );
            // Enqueue Scripts
            wp_enqueue_script( 'THEMENAMEscripts-min' );
        }
    }
}

// Load THEMENAME conditional scripts
function THEMENAME_conditional_scripts() {
    if ( is_page( 'pagenamehere' ) ) {
        // Conditional script(s)
        wp_register_script( 'scriptname', get_template_directory_uri() . '/source/js/scriptname.js', array( 'jquery' ), '1.0.0' );
        wp_enqueue_script( 'scriptname' );
    }
}

// Load THEMENAME styles
function THEMENAME_styles() {
    if ( THEMENAME_DEBUG ) {
        // normalize-css
        wp_register_style( 'normalize', get_template_directory_uri() . '/source/css/lib/normalize.css', array(), '7.0.0' );

        // Custom CSS
        wp_register_style( 'THEMENAME', get_template_directory_uri() . '/style.css', array( 'normalize' ), '1.0' );

        // Register CSS
        wp_enqueue_style( 'THEMENAME' );
    } else {
        // Custom CSS
        wp_enqueue_style( 'THEMENAMEcssmin' );
    }
}

// Register THEMENAME Navigation
function register_THEMENAME_menu() {
    register_nav_menus( array( // Using array to specify more menus if needed
        'header-menu'  => esc_html( 'Header Menu', 'THEMENAME' ), // Main Navigation
        'extra-menu'   => esc_html( 'Extra Menu', 'THEMENAME' ) // Extra Navigation if needed (duplicate as many as you need!)
    ) );
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function THEMENAME_nav_menu_args( $args = '' ) {
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function THEMENAME_css_attributes_filter( $var ) {
    return is_array( $var ) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list( $thelist ) {
    return str_replace( 'rel="category tag"', 'rel="tag"', $thelist );
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class( $classes ) {
    global $post;
    if ( is_home() ) {
        $key = array_search( 'blog', $classes, true );
        if ( $key > -1 ) {
            unset( $classes[$key] );
        }
    } elseif ( is_page() ) {
        $classes[] = sanitize_html_class( $post->post_name );
    } elseif ( is_singular() ) {
        $classes[] = sanitize_html_class( $post->post_name );
    }

    return $classes;
}

// Remove the width and height attributes from inserted images
function remove_width_attribute( $html ) {
    $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
    return $html;
}


// If Dynamic Sidebar Exists
if ( function_exists( 'register_sidebar' ) ) {
    // Define Sidebar Widget Area 1
    register_sidebar( array(
        'name'          => esc_html( 'Widget Area 1', 'THEMENAME' ),
        'description'   => esc_html( 'Description for this widget-area...', 'THEMENAME' ),
        'id'            => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ) );

    // Define Sidebar Widget Area 2
    register_sidebar( array(
        'name'          => esc_html( 'Widget Area 2', 'THEMENAME' ),
        'description'   => esc_html( 'Description for this widget-area...', 'THEMENAME' ),
        'id'            => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ) );
}

// Remove wp_head() injected Recent Comment styles
function THEMENAME_remove_recent_comments_style() {
    global $wp_widget_factory;

    if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
        remove_action( 'wp_head', array(
            $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
            'recent_comments_style'
        ) );
    }
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function THEMENAMEwp_pagination() {
    global $wp_query;
    $big = 999999999;
    echo paginate_links( array(
        'base'    => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
        'format'  => '?paged=%#%',
        'current' => max( 1, get_query_var( 'paged' ) ),
        'total'   => $wp_query->max_num_pages,
    ) );
}

// Create 20 Word Callback for Index page Excerpts, call using THEMENAMEwp_excerpt('THEMENAMEwp_index');
function wp_index( $length ) {
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using THEMENAMEwp_excerpt('THEMENAMEwp_custom_post');
function wp_custom_post( $length ) {
    return 40;
}

// Create the Custom Excerpts callback
function wp_excerpt( $length_callback = '', $more_callback = '' ) {
    if ( function_exists( $length_callback ) )
        add_filter( 'excerpt_length', $length_callback );
    if ( function_exists( $more_callback ) )
        add_filter( 'excerpt_more', $more_callback );

    $output = get_the_excerpt();
    $output = apply_filters( 'wptexturize', $output );
    $output = apply_filters( 'convert_chars', $output );
    $output = '<p>' . $output . '</p>';
    echo esc_html( $output );
}

// Custom View Article link to Post
function THEMENAME_blank_view_article( $more ) {
    global $post;
    return '... <a class="view-article" href="' . get_permalink( $post->ID ) . '">' . esc_html_e( 'View Article', 'THEMENAME' ) . '</a>';
}

// Remove Admin bar
function remove_admin_bar() {
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function THEMENAME_style_remove( $tag ) {
    return preg_replace( '~\s+type=["\'][^"\']++["\']~', '', $tag );
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );
    return $html;
}

// Custom Gravatar in Settings > Discussion
function THEMENAMEgravatar ( $avatar_defaults ) {
    $myavatar                   = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = 'Custom Gravatar';
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments() {
    if ( ! is_admin() ) {
        if ( is_singular() AND comments_open() AND ( get_option( 'thread_comments' ) == 1 ) ) {
            wp_enqueue_script( 'comment-reply' );
        }
    }
}

// Custom Comments Callback
function THEMENAMEcomments( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    extract( $args, EXTR_SKIP );

    if ( 'div' == $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo esc_html( $tag ) ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID(); ?>">
    <?php if ( 'div' != $args['style'] ) : ?>
    <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <?php endif; ?>
    <div class="comment-author vcard">
    <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
    <?php printf( esc_html( '<cite class="fn">%s</cite> <span class="says">says:</span>' ), get_comment_author_link() ) ?>
    </div>
<?php if ( $comment->comment_approved == '0' ) : ?>
    <em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.' ) ?></em>
    <br />
<?php endif; ?>

    <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
        <?php
            printf( esc_html( '%1$s at %2$s' ), get_comment_date(), get_comment_time() ) ?></a><?php edit_comment_link( esc_html_e( '(Edit)' ), '  ', '' );
        ?>
    </div>

    <?php comment_text() ?>

    <div class="reply">
    <?php comment_reply_link( array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) ?>
    </div>
    <?php if ( 'div' != $args['style'] ) : ?>
    </div>
    <?php endif; ?>
<?php }

/*------------------------------------*\
    Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action( 'wp_enqueue_scripts', 'THEMENAME_header_scripts' ); // Add Custom Scripts to wp_head
add_action( 'wp_print_scripts', 'THEMENAME_conditional_scripts' ); // Add Conditional Page Scripts
add_action( 'get_header', 'enable_threaded_comments' ); // Enable Threaded Comments
add_action( 'wp_enqueue_scripts', 'THEMENAME_styles' ); // Add Theme Stylesheet
add_action( 'init', 'register_THEMENAME_menu' ); // Add THEMENAME Menu
add_action( 'init', 'create_post_type_THEMENAME' ); // Add our THEMENAME Custom Post Type
add_action( 'widgets_init', 'THEMENAME_remove_recent_comments_style' ); // Remove inline Recent Comment Styles from wp_head()
add_action( 'init', 'THEMENAMEwp_pagination' ); // Add our THEMENAME Pagination

// Remove Actions
remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

// Add Filters
add_filter( 'avatar_defaults', 'THEMENAMEgravatar' ); // Custom Gravatar in Settings > Discussion
add_filter( 'body_class', 'add_slug_to_body_class' ); // Add slug to body class (Starkers build)
add_filter( 'widget_text', 'do_shortcode' ); // Allow shortcodes in Dynamic Sidebar
add_filter( 'widget_text', 'shortcode_unautop' ); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter( 'wp_nav_menu_args', 'THEMENAME_nav_menu_args' ); // Remove surrounding <div> from WP Navigation
// add_filter( 'nav_menu_css_class', 'my_css_attributes_filter', 100, 1 ); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter( 'nav_menu_item_id', 'my_css_attributes_filter', 100, 1 ); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter( 'page_css_class', 'my_css_attributes_filter', 100, 1 ); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter( 'the_category', 'remove_category_rel_from_category_list' ); // Remove invalid rel attribute
add_filter( 'the_excerpt', 'shortcode_unautop' ); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter( 'the_excerpt', 'do_shortcode' ); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter( 'excerpt_more', 'THEMENAME_blank_view_article' ); // Add 'View Article' button instead of [...] for Excerpts
add_filter( 'show_admin_bar', 'remove_admin_bar' ); // Remove Admin bar
add_filter( 'style_loader_tag', 'THEMENAME_style_remove' ); // Remove 'text/css' from enqueued stylesheet
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 ); // Remove width and height dynamic attributes to thumbnails
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter( 'the_excerpt', 'wpautop' ); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode( 'THEMENAME_shortcode_demo', 'THEMENAME_shortcode_demo' ); // You can place [THEMENAME_shortcode_demo] in Pages, Posts now.
add_shortcode( 'THEMENAME_shortcode_demo_2', 'THEMENAME_shortcode_demo_2' ); // Place [THEMENAME_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [THEMENAME_shortcode_demo] [THEMENAME_shortcode_demo_2] Here's the page title! [/THEMENAME_shortcode_demo_2] [/THEMENAME_shortcode_demo]

/*------------------------------------*\
    Custom Post Types
\*------------------------------------*/

// Create 1 Custom Post type for a Demo, called THEMENAME
function create_post_type_THEMENAME() {
    register_taxonomy_for_object_type( 'category', 'THEMENAME' ); // Register Taxonomies for Category
    register_taxonomy_for_object_type( 'post_tag', 'THEMENAME' );
    register_post_type( 'THEMENAME', // Register Custom Post Type
        array(
        'labels'       => array(
            'name'               => esc_html( 'THEMENAME Custom Post', 'THEMENAME' ), // Rename these to suit
            'singular_name'      => esc_html( 'THEMENAME Custom Post', 'THEMENAME' ),
            'add_new'            => esc_html( 'Add New', 'THEMENAME' ),
            'add_new_item'       => esc_html( 'Add New THEMENAME Custom Post', 'THEMENAME' ),
            'edit'               => esc_html( 'Edit', 'THEMENAME' ),
            'edit_item'          => esc_html( 'Edit THEMENAME Custom Post', 'THEMENAME' ),
            'new_item'           => esc_html( 'New THEMENAME Custom Post', 'THEMENAME' ),
            'view'               => esc_html( 'View THEMENAME Custom Post', 'THEMENAME' ),
            'view_item'          => esc_html( 'View THEMENAME Custom Post', 'THEMENAME' ),
            'search_items'       => esc_html( 'Search THEMENAME Custom Post', 'THEMENAME' ),
            'not_found'          => esc_html( 'No THEMENAME Custom Posts found', 'THEMENAME' ),
            'not_found_in_trash' => esc_html( 'No THEMENAME Custom Posts found in Trash', 'THEMENAME' ),
        ),
        'public'       => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive'  => true,
        'supports'     => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ), // Go to Dashboard Custom THEMENAME post for supports
        'can_export'   => true, // Allows export in Tools > Export
        'taxonomies'   => array(
            'post_tag',
            'category'
        ) // Add Category and Post Tags support
    ) );
}

/*------------------------------------*\
    ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function THEMENAME_shortcode_demo( $atts, $content = null ) {
    return '<div class="shortcode-demo">' . do_shortcode( $content ) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
function THEMENAME_shortcode_demo_2( $atts, $content = null ) {
    return '<h2>' . $content . '</h2>';
}
