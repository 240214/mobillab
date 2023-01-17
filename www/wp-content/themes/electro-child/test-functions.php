<?php

// Step 1: Create the new column itself, it will be empty initially
function create_date_column_for_issues($issue_columns) {
	$issue_columns['date'] = 'Date';
	return $issue_columns;
}
add_filter('manage_edit-issue_columns', 'create_date_column_for_issues');

// Step 2: Populate the new column
function populate_date_column_for_issues($value, $column_name, $term_id) {
	$issue = get_term($term_id, 'issue');
	$date = DateTime::createFromFormat('Ymd', get_field('issue_date', $issue));
	switch($column_name) {
		case 'date':
			$value = $date->format('Y/m/d');
			break;
		default:
			break;
	}
	return $value;
}
add_filter('manage_issue_custom_column', 'populate_date_column_for_issues', 10, 3);

// Step 3: Make the new column sortable
function register_date_column_for_issues_sortable($columns) {
	$columns['date'] = 'issue_date';
	return $columns;
}
add_filter('manage_edit-issue_sortable_columns', 'register_date_column_for_issues_sortable');

// Step 4: Define the custom sorting (where the magic happens)
function sort_issues_by_date($pieces, $taxonomies, $args) {
	global $pagenow;
	if(!is_admin()) {
		return $pieces;
	}

	if(is_admin() && $pagenow == 'edit-tags.php' && $taxonomies[0] == 'issue' && (!isset($_GET['orderby']) || $_GET['orderby'] == 'issue_date')) {
		$pieces['join']   .= " INNER JOIN wp_options AS opt ON opt.option_name = concat('issue_',t.term_id,'_issue_date')";
		$pieces['orderby'] = "ORDER BY opt.option_value";
		$pieces['order']   = isset($_GET['order']) ? $_GET['order'] : "DESC";
	}

	return $pieces;
}
add_filter('terms_clauses', 'sort_issues_by_date', 10, 3);