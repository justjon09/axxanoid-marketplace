<?php
/**
 * Creates the WP_List_Table for Axxanoid Marketplace Makers
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Axxanoid_Marketplace_Makers_List_Table
 *
 * Renders the admin list table for marketplace makers
 */
class Axxanoid_Marketplace_Makers_List_Table extends WP_List_Table {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'maker',
			'plural'   => 'makers',
			'ajax'     => false,
		) );
	}

	/**
	 * Define the columns for the list table.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'     	 => '<input type="checkbox" />',
			'title'      => 'Maker / Brand Name',
			'status'     => 'Marketplace Status',
			'expiration' => 'Trial Expiration',
			'brand_id'   => 'Woo Brand ID',
			'order_id'   => 'Active Order ID',
			'added'		 => 'Date added',
		);
	}

	/**
	 * Define which columns are sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'title'  => array( 'title', true ),
			'status' => array( 'status', true ),
			'added'   => array( 'added', true ),
		);
	}

	/**
	 * Prepare the items for the table to process.
	 */
	public function prepare_items() {
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$per_page     = 20;
		$current_page = $this->get_pagenum();

		$query_args = array(
			'post_type'      => 'axx_market_maker',
			'posts_per_page' => $per_page,
			'paged'          => $current_page,
			'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'orderby'        => isset( $_REQUEST['orderby'] ) ? sanitize_key( $_REQUEST['orderby'] ) : 'date',
			'order'          => isset( $_REQUEST['order'] ) ? sanitize_key( $_REQUEST['order'] ) : 'DESC',
		);

		$query = new WP_Query( $query_args );

		$this->items = $query->posts;

		$this->set_pagination_args(
			array(
				'total_items' => $query->found_posts,
				'per_page'    => $per_page,
				'total_pages' => $query->max_num_pages,
			)
		);
	}

	/**
	 * Render the checkbox column.
	 *
	 * @param WP_Post $item The current item.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="directory_listing[]" value="%s" />', $item->ID );
	}

	/**
	 * Render the title column with row actions.
	 *
	 * @param WP_Post $item The current item.
	 * @return string
	 */
	public function column_title( $item ) {
		$edit_link   = get_edit_post_link( $item->ID );
		$delete_link = get_delete_post_link( $item->ID );
		$title       = get_the_title( $item->ID );
		$post_status = get_post_status_object( get_post_status( $item->ID ) );

		if ( $post_status && 'publish' !== $post_status->name ) {
			$title .= ' &mdash; <span class="post-state">' . esc_html( $post_status->label ) . '</span>';
		}

		$actions = array(
			'view'  => sprintf( '<a href="%s" target="_blank">View Store</a>', esc_url( get_permalink( $item->ID ) ) ),
			'edit'  => sprintf( '<a href="%s">Edit</a>', esc_url( $edit_link ) ),
			'trash' => sprintf( '<a href="%s" class="submitdelete">Trash</a>', esc_url( $delete_link ) ),
		);

		return sprintf( '<strong><a class="row-title" href="%s">%s</a></strong>%s', esc_url( $edit_link ), esc_html( $title ), $this->row_actions( $actions ) );
	}

	/**
	 * Render the status column.
	 *
	 * @param WP_Post $item The current item.
	 * @return string
	 */
	public function column_status( $item ) {
		$display_color = 'black';
		$maker_status = strtolower( get_post_meta( $item->ID, 'marketplace_status', true ) ?: 'Trial');
        
		if ( ! empty( $maker_status ) ) {
			if ($maker_status == 'trial') {
				$display_color = 'grey';
			} else if ($maker_status == 'active') {
				$display_color = 'green';
			}
        }

		return sprintf('<span class="maker-status" style="color:%s">%s</span>', $display_color, esc_html( $maker_status ));
	}

	/**
	 * Default column rendering.
	 *
	 * @param object $item
	 * @param string $column_name
	 * @return mixed
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'added':
				$added = get_the_date( '', $item->ID );
				return $added ? esc_html( $added ) : '&mdash;';
			case 'expiration':
				$paid_exp = get_post_meta( $item->ID, 'paid_expiration_date', true );
				if ( $paid_exp ) {
					return esc_html( $paid_exp ) . ' <small>(Paid)</small>';

				} else  {
					$exp = get_post_meta( $item->ID, 'trial_expiration_date', true );
					return $exp ? esc_html( $exp ) . ' <small>(Trial)</small>' : '&mdash;';
				}				
			case 'brand_id':
				$brand = get_post_meta( $item->ID, 'woo_brand_id', true );
				return $brand ? esc_html( $brand ) : '&mdash;';
			case 'order_id':
				$order = get_post_meta( $item->ID, 'subscription_order_id', true );
				return $order ? esc_html( $order ) : '&mdash;';
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting.
		}
	}
}