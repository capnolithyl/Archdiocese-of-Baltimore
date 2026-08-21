The block editor JavaScript file at `../../javascript/block-editor.js` will be
processed by esbuild, and the output file will be created in this folder as
`block-editor.min.js`. It is enqueued by default in `../functions.php`.

If you add new files to be processed by esbuild, place them in the
`../../javascript` folder and add them to the `development:esbuild` script in
your `package.json` file.

DO NOT directly edit `*.min.js` files, as these files are ignored by git and
will be overwritten the next time esbuild runs.
