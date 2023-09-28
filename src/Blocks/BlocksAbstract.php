<?php

namespace TestBlocks\Blocks;

use TestBlocks\Contracts\Blocks\DynamicBlockTemplateInterface;
use TestBlocks\Core;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use TestBlocks\Traits\SingletonTrait;
use TestBlocks\Traits\BlockFunctionsTrait;

abstract class BlocksAbstract implements DynamicBlockTemplateInterface
{
    use SingletonTrait;

    use BlockFunctionsTrait;

    protected string $viewsDir;

    protected Environment $twig;

    // Deprecated
    protected string $blockName;

    protected array $attributes;

    protected string $template;

    private $postNotUpdate = true;

    public function __construct()
    {
        $core           = Core::getInstance();
        $this->viewsDir = $core::PLUGIN_DIR . 'views/blocks/';
        $twig_options   = [];
        if ( in_array( wp_get_environment_type(), [ 'development', 'local' ] ) ) {
            $twig_options = [ 'debug' => true ];
        }
        $this->twig = new Environment( new FilesystemLoader( $this->viewsDir ), $twig_options );

        add_action( "pre_post_update", [$this, 'postUpdate' ], 10, 2);   
    }

    public function postUpdate() {
        $this->postNotUpdate = false;
    }

    /**
     * @inheritDoc
     */
    public function registerDynamicBlock( string $blockRelativePath = '' )
    {

        // Only load if Gutenberg is available.
        if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
            return;
        }

        // Full path to current block.json file
        $blockFullPath = TEST_BLOCKS_DIR . 'blocks/src/' . trailingslashit( $blockRelativePath ) . 'block.json';

        if ( ! file_exists( $blockFullPath ) ) {
            return;
        }

        // Hook server side rendering into render callback with block metadata from block.json file
        register_block_type_from_metadata(
            $blockFullPath,
            [
                // Deprecated
                //'attributes' => $this->getAttributes(),
                'render_callback' => [ $this, 'renderDynamicBlock' ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function registerStaticBlock( string $blockRelativePath = '' )
    {

        // Only load if Gutenberg is available.
        if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
            return;
        }

        // Full path to current block.json file
        $blockFullPath = TEST_BLOCKS_DIR . 'blocks/src/' . trailingslashit( $blockRelativePath ) . 'block.json';

        if ( ! file_exists( $blockFullPath ) ) {
            return;
        }

        // Hook server side rendering into render callback with block metadata from block.json file
        register_block_type_from_metadata(
            $blockFullPath,
        );
    }

    /**
     * @inheritDoc
     */
    public function renderDynamicBlock( array $attributes, string $renderCallbackContent ): string
    {
        $attributes = $this->setTemplateAttributes( $attributes );

        if ( $renderCallbackContent ) {
            $attributes['renderCallbackContent'] = $renderCallbackContent;
        }

        $this->setTemplate( $attributes );

        return $this->twig->render( $this->template, $attributes );
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    abstract protected function setTemplateAttributes( array $attributes );

    /**
     * @param array $attributes
     *
     * @return mixed
     */
    abstract protected function setTemplate( array $attributes );

    /**
     * Deprecated
     * @return string
     */
    protected function getBlockName(): string
    {
        return $this->blockName ?? '';
    }

    /**
     * Deprecated
     * @return array
     */
    protected function getAttributes(): array
    {
        return $this->attributes ?? [];
    }
}
