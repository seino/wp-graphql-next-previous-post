<?php

namespace WPGraphQL\Extensions\NextPreviousPost;

use WPGraphQL\AppContext;
use WPGraphQL\Model\Post;

/**
 * Class Loader
 *
 * This class allows you to see the next and previous posts in the 'post' type.
 * Added functionality to also get next and previous posts within the same category.
 *
 * @package WNextPreviousPost
 * @since   0.1.0
 */
class Loader
{
    public static function init()
    {
        define('WP_GRAPHQL_NEXT_PREVIOUS_POST', 'initialized');
        (new Loader())->bind_hooks();
    }

    public function bind_hooks()
    {
        add_action(
            'graphql_register_types',
            [$this, 'npp_action_register_types'],
            9,
            0
        );
    }

    public function npp_action_register_types()
    {
        $postTypes = get_post_types(
            ['public' => true, '_builtin' => false],
            'objects'
        );
        $typeNames = array_filter(
            array_map(function ($postType) {
                return isset($postType->graphql_single_name)
                    ? ucfirst($postType->graphql_single_name)
                    : null;
            }, $postTypes)
        );

        $typeNames[] = 'Post';

        foreach ($typeNames as $typeName) {
            // Standard next post field (existing functionality)
            register_graphql_field($typeName, 'next', [
                'type' => $typeName,
                'description' => __('Next post'),
                'resolve' => function (
                    Post $post,
                    array $args,
                    AppContext $context
                ) {
                    global $post;

                    // get post
                    $post = get_post($post->ID, OBJECT);

                    // setup global $post variable
                    setup_postdata($post);

                    $next = get_next_post();

                    wp_reset_postdata();

                    if (!$next) {
                        return null;
                    }

                    return $context
                        ->get_loader('post')
                        ->load_deferred($next->ID);
                },
            ]);

            // Standard previous post field (existing functionality)
            register_graphql_field($typeName, 'previous', [
                'type' => $typeName,
                'description' => __('Previous post'),
                'resolve' => function (
                    Post $post,
                    array $args,
                    AppContext $context
                ) {
                    global $post;

                    // get post
                    $post = get_post($post->ID, OBJECT);

                    // setup global $post variable
                    setup_postdata($post);

                    $prev = get_previous_post();

                    wp_reset_postdata();

                    if (!$prev) {
                        return null;
                    }

                    return $context
                        ->get_loader('post')
                        ->load_deferred($prev->ID);
                },
            ]);

            // Next post in the same category field (new functionality)
            register_graphql_field($typeName, 'nextInCategory', [
                'type' => $typeName,
                'description' => __('Next post in the same category'),
                'args' => [
                    'taxonomy' => [
                        'type' => 'String',
                        'description' => __('Taxonomy name. Default is "category".'),
                        'defaultValue' => 'category'
                    ]
                ],
                'resolve' => function (
                    Post $post,
                    array $args,
                    AppContext $context
                ) {
                    global $post;

                    // get post
                    $post = get_post($post->ID, OBJECT);

                    // setup global $post variable
                    setup_postdata($post);

                    // Get taxonomy from args or use default
                    $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category';

                    // Get next post in same category
                    $next = get_next_post(true, '', $taxonomy);

                    wp_reset_postdata();

                    if (!$next) {
                        return null;
                    }

                    return $context
                        ->get_loader('post')
                        ->load_deferred($next->ID);
                },
            ]);

            // Previous post in the same category field (new functionality)
            register_graphql_field($typeName, 'previousInCategory', [
                'type' => $typeName,
                'description' => __('Previous post in the same category'),
                'args' => [
                    'taxonomy' => [
                        'type' => 'String',
                        'description' => __('Taxonomy name. Default is "category".'),
                        'defaultValue' => 'category'
                    ]
                ],
                'resolve' => function (
                    Post $post,
                    array $args,
                    AppContext $context
                ) {
                    global $post;

                    // get post
                    $post = get_post($post->ID, OBJECT);

                    // setup global $post variable
                    setup_postdata($post);

                    // Get taxonomy from args or use default
                    $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category';

                    // Get previous post in same category
                    $prev = get_previous_post(true, '', $taxonomy);

                    wp_reset_postdata();

                    if (!$prev) {
                        return null;
                    }

                    return $context
                        ->get_loader('post')
                        ->load_deferred($prev->ID);
                },
            ]);
        }
    }
}
