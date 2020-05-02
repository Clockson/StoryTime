<?php

class StoryTimeActivator {
    public static function activate() {
        StoryTimeActivator::create_page();
    }
    
    private static function create_page() {
        $pageName = "Story Time";
        $pageSlug = "storytime";
        $pageData = get_page_by_path( $pageSlug );

        $template = '[md-story-time]';

        // reset the page if it already exists to avoid stale instances
        if ( $pageData != null ){
            wp_delete_post( $pageData->ID, true );
        }

        wp_insert_post(
            array(
                'post_content' => $template,
                'post_title' => $pageName,
                'post_name' => $pageSlug,
                'post_status' => 'publish',
                'post_type' => 'page'
            )
        );

    }
}