<?php
/*
Plugin Name: Random Redirect 2
Plugin URI: http://wordpress.org/extend/plugins/random-redirect-2/
Description:This is an expansion of Matt Mullenweg's original Random Redirect plugin which allowed you to create a link to link to yourblog.example.com/?random which would redirect the visitor to a random post or page on your website in a manner similar to StumbleUpon.  Matt's original plugin also allowed you to limit the randomness to specific categories and post/page status.

We have taken the original plugin a step further, with Random Redirect 2 you can specify `random_tag_id` or `random_year` or `random_month` or `random_keyword` too, giving you fine grained control of the level of randomness.  In addition to the extra parameters, you can also combine parameters!
Version: 1
Author: Chris Adams
Author URI: http://showappeal.com/
*/

function matt_random_redirect() {
	global $wpdb;
	
	$query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";

	if ( isset( $_GET['random_cat_id'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_tag_id'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_year'] ) ) {
		$random_year = (int) $_GET['random_year'];
		$query = "SELECT ID FROM $wpdb->posts WHERE year(post_date) = $random_year AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_month'] ) ) {
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_keyword'] ) ) {
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT ID FROM $wpdb->posts WHERE $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );

		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];

		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_year'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_year = (int) $_GET['random_year'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE year(post_date) = $random_year AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];

		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];

			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE month(post_date) = $random_month AND  post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);

			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE monthname(post_date) = '$random_month' AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";

		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_year'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_year = (int) $_GET['random_year'];
		$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND year(post_date) = $random_year AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_month'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_keyword'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE year(post_date) = $random_year AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_tag_id'] ) && isset( $_GET['random_month'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];

		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE month(post_date) = $random_month AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE monthname(post_date) = '$random_month' AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_tag_id'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

    if ( isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE year(post_date) = $random_year AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT ID FROM $wpdb->posts WHERE year(post_date) = $random_year AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];

		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_year'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_year = (int) $_GET['random_year'];

		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];

		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND year(post_date) = $random_year AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE year(post_date) = $random_year AND month(post_date) = $random_month AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE year(post_date) = $random_year AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];

			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE month(post_date) = $random_month AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);

			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE monthname(post_date) = '$random_month' AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND year(post_date) = $random_year AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_month'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_keyword'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND year(post_date) = $random_year AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE year(post_date) = $random_year AND month(post_date) = $random_month AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE year(post_date) = $random_year AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_tag_id'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE month(post_date) = $random_month AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE monthname(post_date) = '$random_month' AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

   if ( isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE year(post_date) = $random_year AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND year(post_date) = $random_year AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND year(post_date) = $random_year AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE year(post_date) = $random_year AND month(post_date) = $random_month AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND year(post_date) = $random_year AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY 
		RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE year(post_date) = $random_year AND month(post_date) = $random_month AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND $random_keyword AND post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND year(post_date) = $random_year AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}
	
	if ( isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = '$post_type' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
		}
	}

	if ( isset( $_GET['random_cat_id'] ) && isset( $_GET['random_post_type'] ) && isset( $_GET['random_tag_id'] ) && isset( $_GET['random_year'] ) && isset( $_GET['random_month'] ) && isset( $_GET['random_keyword'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$random_tag_id = (int) $_GET['random_tag_id'];
		$random_year = (int) $_GET['random_year'];
		$keywords = explode(' ', $_GET['random_keyword']);
		foreach ($keywords as &$keyword) 
			$keyword = "concat(post_title,post_content) like '%$keyword%'";
		$random_keyword = "(".implode(" and ", $keywords).")";
		if ( is_numeric($_GET['random_month']) ) {
			$random_month = (int) $_GET['random_month'];
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND month(post_date) = $random_month AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		} else {
			$random_month = preg_replace( '|[^a-z]|i', '', $_GET['random_month']);
			$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = '$post_type' AND year(post_date) = $random_year AND monthname(post_date) = '$random_month' AND $random_keyword AND post_password = '' AND 	post_status = 'publish' AND ID in (SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN  $wpdb->term_taxonomy AS tt ON(tt.term_id = $random_tag_id AND taxonomy = 'post_tag') INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish') ORDER BY RAND() LIMIT 1";
		}
	}

	$random_id = $wpdb->get_var( $query );

    wp_redirect( get_permalink( $random_id ) );
	exit;
}

if ( isset( $_GET['random'] ) )
	add_action( 'template_redirect', 'matt_random_redirect' );

?>