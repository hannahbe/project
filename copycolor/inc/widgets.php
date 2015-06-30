<?php

/********** CATEGORIES WIDGETS **********/

//exclude none and catalog category and subcategories from the categories widget
function exclude_widget_categories($args){
    $exclude = "1,22,23,24,25"; // The IDs of the excluding categories : None, Catalog, Catalog-Design, Catalog-Print, Catalog-Subimation
    $args["exclude"] = $exclude;
    return $args;
}
add_filter("widget_categories_args","exclude_widget_categories");


/********** ARCHIVES WIDGETS **********/

//the following includes only posts from category gallery and its subcategories in the archives widget
define("INCLUDED_CATEGORIES", '14,15,16,17');

add_filter( 'getarchives_join' , 'getarchives_join_filter');
function getarchives_join_filter( $join ) {
	global $wpdb;
	return $join . " INNER JOIN {$wpdb->term_relationships} tr ON ($wpdb->posts.ID = tr.object_id) INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";
}

add_filter( 'getarchives_where' , 'getarchives_where_filter');
function getarchives_where_filter( $where ) {
	global $wpdb;

	$include = INCLUDED_CATEGORIES; // category ids to include
	return $where . " AND tt.taxonomy = 'category' AND tt.term_id IN ($include)";

	}

// exclude categories on monthly archive pages
function my_post_queries( $query ) {
	// do not alter the query on wp-admin pages and only alter it if it's the main query
	if (!is_admin() && $query->is_main_query()){

		// alter the query for monthly archive pages
		if(is_archive() && is_month()){
			$query->set('in_category', array(INCLUDED_CATEGORIES));
		}
	}
}

add_action( 'pre_get_posts', 'my_post_queries' );

?>

