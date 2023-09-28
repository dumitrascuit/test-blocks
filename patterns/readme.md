# Registered Block Patterns
**Folder for load and register patterns automatically.**

## Adding new pattern
1. Create a folder with the name of the pattern.
2. Add new Group and blocks in Block Editor, add group unique css class in options, use 'madl-patterns-' namespace prefix, see [Naming conventions](#naming-conventions)
3. Create 'meta.json' file with the following structure:

```
{
  "name": "madl-patterns/your-pattern-folder-name",
  "label": "Some human readable pattern label",
  "description": "Pattern description",
  "class": "madl-patterns-your-pattern-folder-name",
  "patternsViews": {
    "first-view-name": {
      "title": "First view title",
      "editorScript": "file:build/first-view-name-editor.js",
      "script": "file:build/first-view-name-script.js",
      "viewScript": "file:build/first-view-name-view.js",
      "editorStyle": "file:build/first-view-name-editor.css",
      "style": "file:build/first-view-name.css"
    },
    "second-view-name": {
      "title": "Second view title",
      "editorScript": "file:build/second-view-name-editor.js",
      "script": "file:build/second-view-name-script.js",
      "viewScript": "file:build/second-view-name-view.js",
      "editorStyle": "file:build/second-view-name-editor.css",
      "style": "file:build/second-view-name.css"
    }
  },
  "editorScript": "file:build/editor.js",
  "script": "file:build/script.js",
  "viewScript": "file:build/view.js",
  "editorStyle": "file:build/editor.css",
  "style": "file:build/style.css"
}
```
It's the same as 'block.json' for blocks https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/

Each pattern view can have its own js and css files. This files will automatically be enqueued in the editor and frontend when the pattern is used on the page.

**Required fields:**

- name
- label
- patternsViews
  - title 

3. Add views (patterns) twig files. Follow the pattern name as the file name and as 'patternsViews' option in 'meta.json'.
4. Paste pattern template html code from Blocks editor to twig file.
5. Add scss and js files to 'src' folder. Follow the pattern name as the file name and as 'patternsViews' option in 'meta.json'.
6. Run `npm run watch` to build the assets.

## Add theme support
To use only specific patterns views in the theme: 

1. Add theme support in 'after_setup_theme' action:

```
add_theme_support( 'madl-patterns',
    [
        'madl-patterns/your-pattern-folder-name__first-view-name' => [
            'editorScript' => 'file:build/first-view-name-editor.js',
            'script'       => 'file:build/first-view-name-script.js',
            'viewScript'   => 'file:build/first-view-name-view.js',
            'editorStyle'  => 'file:build/first-view-name-editor.css',
            'style'        => 'file:build/dark-style.css'
        ],
        'madl-patterns/your-pattern-folder-name__second-view-name' => [],
    ]
);
```
2. Add your styles and scripts to the theme folder: 'theme-folder/patterns/madl-patterns/your-pattern-folder/src'
It will compile and load automatically in the editor and frontend if pattern is used.

## Naming conventions

⚠️ **WARNING Do not rename patterns view twig files** 

They are used to load the pattern template that are stored in database. So if you will rename patterns twig template files, your already added patterns will lose styles, and you will need to update the database manually.

⚠️ **WARNING Do not change pattern category name (folder name) and pattern view file names.
It will break all existing patterns** 

**📣 Please follow the pattern name convention: 'pattern-name' (lowercase, dash-separated)** 

**📣 Pay attention to 'class' option:** 
1. It should same in `meta.json`, in `.twig` file and in `.css` file.
2. It should be unique for each pattern.
3. View name should be added to the class name with two underscores '__view-name' in additional `.twig` files with additional classes.
