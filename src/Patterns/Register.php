<?php

namespace TestBlocks\Patterns;

use TestBlocks\Contracts\ControllerInitHooksInterface;
use TestBlocks\Core;
use TestBlocks\Helpers\LoggerHelper;
use TestBlocks\Helpers\PatternsHelper;
use TestBlocks\Traits\SingletonTrait;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use WP_Block_Pattern_Categories_Registry;
use WP_Block_Patterns_Registry;

class Register implements ControllerInitHooksInterface
{
    use SingletonTrait;

    /**
     * Block patterns views dir
     * @var string
     */
    protected string $patternsDir;

    /**
     * @var Environment
     */
    protected Environment $twig;

    /**
     * @var LoggerHelper
     */
    private LoggerHelper $logger;

    /**
     * Theme supported patterns
     * @var array
     */
    private array $supportedPatterns;

    /**
     * Blocks Patterns Register constructor.
     */
    public function __construct()
    {
        $this->patternsDir = Core::PLUGIN_DIR . 'patterns/';

        $twigOptions = [];
        if ( in_array( wp_get_environment_type(), [ 'development', 'local' ] ) ) {
            $twigOptions = [ 'debug' => true ];
        }
        $this->twig   = new Environment( new FilesystemLoader( $this->patternsDir ), $twigOptions );
        $this->logger = new LoggerHelper();
    }

    /**
     * Register block patterns
     */
    public function initHooks()
    {
        add_action( 'init', [ $this, 'getSupportedPatterns' ] );
        add_action( 'init', [ $this, 'registerPatternsWithCategories' ] );
    }

    /**
     * Get patterns list supported by current theme
     */
    public function getSupportedPatterns()
    {
        $supportedPatterns       = get_theme_support( 'madl-patterns' );
        $this->supportedPatterns = $supportedPatterns[0] ?? [];
    }

    /**
     * Load and register block patterns and blocks patterns categories
     * Looking patterns categories in folder 'patterns/<pattern-name>' in plugin root
     * Patterns views situated in folder 'patterns/<pattern-name>/views'
     * Please follow the pattern name convention: 'pattern-name' (lowercase, dash-separated)
     */
    public function registerPatternsWithCategories(): void
    {
        $patternCategories = glob( $this->patternsDir . '*', GLOB_ONLYDIR );

        foreach ( $patternCategories as $patternCategoryDir ) {

            $patternCategoryMeta = $this->getPatternCategoryMetadata( $patternCategoryDir );

            if (
                empty( $patternCategoryMeta ) ||
                empty( $patternCategoryMeta['name'] ) ||
                empty( $patternCategoryMeta['label'] ) ||
                empty( $patternCategoryMeta['class'] )
            ) {
                continue;
            }

            if (
                ! empty( $this->supportedPatterns ) &&
                ! array_key_exists( $patternCategoryMeta['name'], $this->supportedPatterns )
            ) {
                continue;
            }

            // Register pattern category assets
            $assets = $this->getPatternAssets(
                $patternCategoryMeta,
                $patternCategoryMeta['file']
            );

            // Register pattern category assets from theme
            $themeAssets = $this->getPatternAssets(
                [
                    'name'         => $patternCategoryMeta['name'] . '__theme',
                    'editorScript' => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['editorScript'] ?? '',
                    'script'       => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['script'] ?? '',
                    'viewScript'   => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['viewScript'] ?? '',
                    'editorStyle'  => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['editorStyle'] ?? '',
                    'style'        => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['style'] ?? '',
                ],
                realpath( get_stylesheet_directory() . '/patterns/' . $patternCategoryMeta['name'] . '/build' ) // it will take directory  - dirname( $metadata['file'] )
            );

            $patternAttributes = PatternsHelper::getPatternAttributes(
                [
                    'label' => $patternCategoryMeta['label'],
                    'class' => $patternCategoryMeta['class'],
                ],
                $assets,
                $themeAssets
            );

            WP_Block_Pattern_Categories_Registry::get_instance()->register(
                $patternCategoryMeta['name'],
                $patternAttributes
            );

            $this->registerPatterns( $patternCategoryDir, $patternCategoryMeta );

        }

    }

    /**
     * Register block patterns in current category
     */
    public function registerPatterns( $patternCategoryDir, $patternCategoryMeta ): void
    {
        if ( empty( $patternCategoryMeta['patternsViews'] ) ) {
            return;
        }


        foreach ( $patternCategoryMeta['patternsViews'] as $view => $patternViewMeta ) {

            $patternViewMeta['name']  = $patternCategoryMeta['name'] . '__' . $view;
            $patternViewMeta['class'] = $patternCategoryMeta['class'] . '__' . $view;

            if (
                ! empty( $this->supportedPatterns ) &&
                ! array_key_exists( $view, $this->supportedPatterns[ $patternCategoryMeta['name'] ]['views'] )
            ) {
                continue;
            }

            $viewFile = basename( $patternCategoryDir ) . '/views/' . $view . '.html.twig';

            // Warning! Do not change pattern category name (folder name) and pattern view file names.
            // It will break all existing patterns
            try {
                $content = $this->twig->render( $viewFile );
            } catch ( Exception $e ) {
                // We can call fatal error here, but it's not good idea
                //throw new \Exception($e->getMessage() );
                $this->logger->log(
                    $e->getMessage(),
                    $e->getCode()
                );
                continue;
            }

            // Register pattern assets from plugin
            $assets = $this->getPatternAssets(
                $patternViewMeta,
                $patternCategoryMeta['file']
            );

            // Register pattern assets from theme
            $themeAssets = $this->getPatternAssets(
                [
                    'name'         => $patternViewMeta['name'] . '__theme',
                    'editorScript' => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['views'][ $view ]['editorScript'] ?? '',
                    'script'       => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['views'][ $view ]['script'] ?? '',
                    'viewScript'   => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['views'][ $view ]['viewScript'] ?? '',
                    'editorStyle'  => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['views'][ $view ]['editorStyle'] ?? '',
                    'style'        => $this->supportedPatterns[ $patternCategoryMeta['name'] ]['views'][ $view ]['style'] ?? '',
                ],
                realpath( get_stylesheet_directory() . '/patterns/' . $patternCategoryMeta['name'] . '/build' ) // it will take directory  - dirname( $metadata['file'] )
            );

            // Merge assets from plugin and theme and register pattern
            $patternAttributes = PatternsHelper::getPatternAttributes(
                [
                    'title'      => $patternViewMeta['title'],
                    'categories' => [ $patternCategoryMeta['name'] ],
                    'content'    => $content,
                    'class'      => $patternViewMeta['class'],
                ],
                $assets,
                $themeAssets
            );

            WP_Block_Patterns_Registry::get_instance()->register(
                $patternViewMeta['name'],
                $patternAttributes
            );
        }
    }

    /**
     * Get pattern category meta from file '/patterns/<pattern-name>/meta.json'
     *
     * @param string $patternCategoryDir
     *
     * @return array
     */
    private function getPatternCategoryMetadata( string $patternCategoryDir ): array
    {
        $patternCategoryMeta = [];

        $metadataFilename = 'meta.json';

        $metadataFile = trailingslashit( $patternCategoryDir ) . $metadataFilename;

        if ( ! file_exists( $metadataFile ) ) {
            return [];
        }

        $metadata = wp_json_file_decode( $metadataFile, [ 'associative' => true ] );
        if ( ! is_array( $metadata ) || empty( $metadata ) ) {
            return [];
        }

        $metadata['file'] = wp_normalize_path( realpath( $metadataFile ) );
        //$metadata['file'] = wp_normalize_path( $metadataFile );

        /* ToDo maybe make some metadata generated automatically? Now we need to add it manually because we have patterns in database
        // All pattern data stored in meta.json file, but some data are generated by function and based on pattern folder name
        // This need to rule out a naming error
        // categoryName = madl-patterns/<folder-name>
        // patternName = madl-patterns/<folder-name>__<view-name>
        // categoryClass = madl-patterns-<folder-name>
        // viewClass = madl-patterns-<folder-name>__<view-name>
        $patternCategoryMeta['name']  = Core::PLUGIN_SHORT_SLUG . '-patterns/' . basename( $patternCategoryDir );
        $patternCategoryMeta['class'] = Core::PLUGIN_SHORT_SLUG . '-patterns-' . basename( $patternCategoryDir );
        */

        if ( empty( $metadata['class'] ) ) {
            $metadata['class'] = str_replace( '/', '-', $metadata['name'] );;
        }

        /**
         * Filters the metadata provided for registering a pattern category.
         *
         * @param array $metadata Metadata for registering a pattern category.
         */
        $metadata = apply_filters( Core::PLUGIN_SLUG . '/pattern_category_metadata', $metadata );

        $propertyMappings = [
            'name'          => 'name', // pattern category name
            'label'         => 'label', // pattern category label
            'description'   => 'description',
            'class'         => 'class', // pattern category class - used in view templates
            'textDomain'    => 'textDomain', // pattern category text domain - not used yet
            'patternsViews' => 'patternsViews', // Views - exactly patterns
            'file'          => 'file', // pattern category meta.json file path (generated by pattern category folder name)
            'editorScript'  => 'editorScript',
            'script'        => 'script',
            'viewScript'    => 'viewScript',
            'editorStyle'   => 'editorStyle',
            'style'         => 'style',
        ];

        // Use textDomain when $i18n_schema will be implemented
        //$textDomain       = ! empty( $metadata['textDomain'] ) ? $metadata['textDomain'] : null;
        // ToDo replace this with special function for patterns metadata
        //$i18n_schema      = get_block_metadata_i18n_schema();


        foreach ( $propertyMappings as $key => $mappedKey ) {
            if ( isset( $metadata[ $key ] ) ) {
                $patternCategoryMeta[ $mappedKey ] = $metadata[ $key ];
                // ToDo uncomment when we will have special function for patterns metadata
                /*if ( $textDomain && isset( $i18n_schema->$key ) ) {
                    $settings[ $mappedKey ] = translate_settings_using_i18n_schema( $i18n_schema->$key, $settings[ $key ], $textDomain );
                }*/
            }
        }

        return $patternCategoryMeta;
    }


    /**
     * Get pattern assets from pattern assets register function
     *
     * @param array $metadata
     * @param string $metaFile
     *
     * @return array
     */
    public function getPatternAssets( array $metadata, string $metaFile ): array
    {
        return PatternsHelper::registerPatternAssets(
            [
                'name'         => $metadata['name'],
                'file'         => $metaFile,
                'editorScript' => $metadata['editorScript'] ?? '',
                'script'       => $metadata['script'] ?? '',
                'viewScript'   => $metadata['viewScript'] ?? '',
                'editorStyle'  => $metadata['editorStyle'] ?? '',
                'style'        => $metadata['style'] ?? '',
            ]
        );
    }
}
