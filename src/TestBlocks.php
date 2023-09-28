<?php

namespace TestBlocks;

use TestBlocks\Blocks\BlockToolsSection;
use TestBlocks\Blocks\BlockToolsSingle;
use TestBlocks\Blocks\BlockColumns;
use TestBlocks\Gutenberg\BlockCategories;
use TestBlocks\RestApiEndpoints\RestApiEndpointCreator;
use TestBlocks\RestApiEndpoints\RestApiEndpointTools;
use TestBlocks\Scripts\ScriptsGutenberg;
use TestBlocks\Traits\SingletonTrait;
use TestBlocks\Actions\RemoveHeaderApiLink;
use TestBlocks\Filters\BlocksRender;
use TestBlocks\Metaboxes\MetaboxTools;
use TestBlocks\PostTypes\PostTypeTools;

/**
 * Class TestBlocks
 * @package TestBlocks
 */
class TestBlocks
{
    use SingletonTrait;

    private const PLUGIN_DIR = Core::PLUGIN_DIR;

    /**
     * TestBlocks constructor.
     */
    private function __construct()
    {
    }

    /**
     * Set plugin required functionality
     */
    public function setInstances()
    {
        // Global

        /**
         * Custom post types
         */

        // Load scripts
        ScriptsGutenberg::getInstance()->initHooks();

        // register tools cpt
        PostTypeTools::getInstance()->initHooks();
        MetaboxTools::getInstance()->initHooks();

        // load blocks
        add_action('init', function () {
            // Blocks
            BlockToolsSection::getInstance()->registerStaticBlock( 'toolsSection/' );        

            // Component blocks        
            BlockToolsSingle::getInstance()->registerDynamicBlock( 'components/toolsSingle/' );
        });

        // Add all rest API points
        add_action( 'rest_api_init', function () {
            $restApiCreator = new RestApiEndpointCreator();
            $restApiCreator->registerRestRoute( new RestApiEndpointTools() );
        } );

        // Add blocks Madl category
        BlockCategories::getInstance()->initHooks();

        // Register all block patterns from ./patterns folder
        Patterns\Register::getInstance()->initHooks();

        // Frontend
        if ( wp_doing_ajax() ) {
            return;
        }

        // Remove api link in header
        RemoveHeaderApiLink::getInstance()->initHooks();

        // Blocks render filters
        BlocksRender::getInstance()->initHooks();
    }
}
