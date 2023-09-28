import endpoints from "../../../modules/restApiEndpoints";

/**
 * Internal component libraries
 */
const {Component} = wp.element;
const {
  SelectControl,
  Spinner
} = wp.components;

const {toolsEndpoint} = endpoints;

export default class SelectTools extends Component {
  constructor() {
    super(...arguments);

    this.state = {
      toolsOptions: []
    };
  }

  componentDidMount() {
    this.runSitesApiFetch();
  }

  runSitesApiFetch() {
    wp.apiFetch({
      path: toolsEndpoint
    }).then(data => {
      this.setState({
        toolsOptions: data        
      });
    });
  }

  render() {
    const {attributes, setAttributes} = this.props;
    const toolsOptions = {...this.state.toolsOptions};

    let selectToolsOptions = [
      {
        label: 'None',
        value: ''
      }
    ].concat(
      Object.keys(toolsOptions).map(key => {        
        return {label: this.state.toolsOptions[key].toolsName, value: this.state.toolsOptions[key].toolsId};
      })
    );

    return [
      selectToolsOptions.length < 1
        ? <Spinner key="siteSpinner" />
        : <SelectControl
          key="toolsSelect"
          label="Tools"
          value={attributes.toolsId}
          options={selectToolsOptions}
          onChange={(toolsId) => {
            setAttributes({toolsId});
          }}
        />
    ];
  }
}
