<?php
/**
 * The template for displaying the public Directory Archive / Search results.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

get_header();

// Catch the search query if coming from the Hub page
$search_query = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
?>

<div class="axx-market-archive-container wrap">

    <div class="axx-market-archive-header">
        <?php if ( $search_query ) : ?>
            <h1>Search Results for "<?php echo esc_html( $search_query ); ?>"</h1>
            <p>Discovering independent artists and gear makers.</p>
        <?php else : ?>
            <h1>Indie Maker Directory</h1>
            <p>Browse the finest underground glassblowers, 3D printers, and accessory creators.</p>
        <?php endif; ?>
    </div>

    <div class="axx-market-search-bar" style="max-width: 600px; margin: 0 auto 40px auto;">
        <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="axx-market-search-form">
            <input type="hidden" name="post_type" value="axx_market_maker" />
            <input type="text" name="s" placeholder="Search for makers, brands, or specific gear..." value="<?php echo esc_attr( $search_query ); ?>" required />
            <button type="submit" class="button button-primary">Search</button>
        </form>
    </div>

    <?php
    // We must query to ONLY show 'Active' and 'Trial' Makers.
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    
    $args = array(
        'post_type'      => 'axx_market_maker',
        'post_status'    => 'publish',
        'posts_per_page' => 24,
        'paged'          => $paged,
        's'              => $search_query, // Pass search parameter natively
        'meta_query'     => array(
            array(
                'key'     => 'marketplace_status',
                'value'   => array( 'Active', 'Trial' ),
                'compare' => 'IN'
            )
        )
    );

    $maker_query = new WP_Query( $args );

    if ( $maker_query->have_posts() ) : ?>
        
        <div class="axx-maker-grid">
            <?php while ( $maker_query->have_posts() ) : $maker_query->the_post(); 
                $maker_id = get_the_ID();
                $banner_id = get_post_meta( $maker_id, 'maker_header_banner', true );
                $portrait_id = get_post_meta( $maker_id, 'maker_portrait', true );
                $callout = get_post_meta( $maker_id, 'maker_callout_text', true );

                $banner_url = $banner_id ? wp_get_attachment_image_url( $banner_id, 'medium_large' ) : AXX_MARKET_PLUGIN_URL . 'public/assets/images/default-banner.svg';
                $portrait_url = $portrait_id ? wp_get_attachment_image_url( $portrait_id, 'thumbnail' ) : AXX_MARKET_PLUGIN_URL . 'public/assets/images/default-avatar.svg';
            ?>
                
                <article class="axx-maker-card">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url( $banner_url ); ?>" class="axx-maker-card-banner" alt="Banner" loading="lazy" />
                    </a>
                    
                    <div class="axx-maker-card-body">
                        <div class="axx-maker-card-portrait-wrap">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
                            </a>
                        </div>
                        
                        <h2 class="axx-maker-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <?php if ( $callout ) : ?>
                            <div class="axx-maker-card-callout">
                                "<?php echo esc_html( wp_trim_words( $callout, 15, '...' ) ); ?>"
                            </div>
                        <?php else : ?>
                            <div class="axx-maker-card-callout">
                                <?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_post_meta( $maker_id, 'maker_bio', true ) ), 15, '...' ) ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="axx-maker-card-action">
                            <a href="<?php the_permalink(); ?>" class="button button-secondary">View Portfolio</a>
                        </div>
                    </div>
                </article>

            <?php endline; ?>
            <?php endwhile; ?>
        </div>

        <div class="axx-pagination" style="text-align: center; margin-top: 40px;">
            <?php 
            echo paginate_links( array(
                'total' => $maker_query->max_num_pages,
                'prev_text' => '&laquo; Previous',
                'next_text' => 'Next &raquo;',
            ) ); 
            ?>
        </div>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>

        <div class="axx-market-empty-state">
            <h2>No Makers Found</h2>
            <p>We couldn't find any indie makers matching your search criteria. Check back soon as we add new artists daily!</p>
            <a href="<?php echo esc_url( home_url( '/marketplace/' ) ); ?>" class="button button-primary" style="margin-top: 20px;">Return to Hub</a>
        </div>

    <?php endif; ?>

</div>

<?php get_footer(); ?>