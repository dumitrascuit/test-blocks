import icon from './icon';
import {blockIconBackgroundColor} from '../../../../modules/commonConsts';
import SelectTools from "../../SelectTools";
import blockMetadata from '../block.json';

/**
 * Internal block libraries
 */
const {registerBlockType} = wp.blocks;
const {
  useBlockProps,
  InspectorControls
} = wp.blockEditor;
const {
  PanelBody,
  TextareaControl
} = wp.components;
const {serverSideRender: ServerSideRender} = wp;

registerBlockType(
  blockMetadata,
  {
    icon: {
      background: blockIconBackgroundColor,
      src: icon
    },
    edit: props => {
      const {attributes, className, setAttributes} = props;

      // https://make.wordpress.org/core/2020/11/18/block-api-version-2/
      const blockProps = useBlockProps({
        className: [className]
      });

      return (
        [
          <InspectorControls key="controls">
            <PanelBody title="Choose Tools">
              <SelectTools {...props} />
              <TextareaControl
                label="Text Descrition"
                help="Enter some text"
                value={attributes.toolsDescription}
                onChange={(toolsDescription) => setAttributes({toolsDescription})}
              />
            </PanelBody>
          </InspectorControls>,
          <div
            {...blockProps}
            key="serverRender"
          >
            <ServerSideRender
              block={blockMetadata.name}
              attributes={attributes}
            />
          </div>
        ]
      );
    }, // end edit
    save: props => {
      // Rendering in PHP
      return null;
    }
  });
