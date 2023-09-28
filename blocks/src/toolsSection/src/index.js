/**
 * Block dependecies
 */
import icon from './icon';
import {blockIconBackgroundColor} from '../../../modules/commonConsts';
import blockMetadata from '../block.json';
import toolsSingleMetadata from '../../components/toolsSingle/block.json';

/**
 * Internal block libraries
 */
const {registerBlockType} = wp.blocks;
const {
  InnerBlocks,
  useBlockProps
} = wp.blockEditor;

const blockMainCssClass = 'ra-tools-section';
const ALLOWED_BLOCKS = [toolsSingleMetadata.name];

registerBlockType(
  blockMetadata,
  {
    icon: {
      background: blockIconBackgroundColor,
      src: icon
    },
    edit: props => {
      const {className} = props;

      // https://make.wordpress.org/core/2020/11/18/block-api-version-2/
      const blockProps = useBlockProps({
        className: [blockMainCssClass, className]
      });

      return [
        <div {...blockProps} key="blockControls">
          <InnerBlocks
            allowedBlocks={ALLOWED_BLOCKS}
            orientation="horizontal"
          />
        </div>
      ];
    },
    save: () => {
      const blockProps = useBlockProps.save({
        className: blockMainCssClass
      });

      return (
        <div {...blockProps}>
          <InnerBlocks.Content />
        </div>
      );
    }
  }
);
