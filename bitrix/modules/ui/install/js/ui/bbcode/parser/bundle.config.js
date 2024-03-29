const fs = require('fs');
const path = require('path');
const webToMobilePlugin = require('../.build-plugins/web-to-mobile');

module.exports = {
	input: 'src/parser.js',
	output: 'dist/parser.bundle.js',
	namespace: 'BX.UI.Bbcode',
	browserslist: true,
	plugins: {
		custom: [
			webToMobilePlugin({
				targetExtension: 'bbcode/parser',
				replacements: [
					[
						fs.readFileSync(path.resolve(__dirname, 'build/chunks/header.web.txt'), 'ascii'),
						fs.readFileSync(path.resolve(__dirname, 'build/chunks/header.mobile.txt'), 'ascii'),
					],
					[
						fs.readFileSync(path.resolve(__dirname, 'build/chunks/footer.web.txt'), 'ascii'),
						fs.readFileSync(path.resolve(__dirname, 'build/chunks/footer.mobile.txt'), 'ascii'),
					],
					[
						/main_core\.Type/g,
						'Type',
					],
					[
						/ui_bbcode_model/g,
						'Model',
					],
				],
				banner: fs.readFileSync(path.resolve(__dirname, 'build/chunks/attention.message.txt'), 'ascii'),
			}),
		],
	},
};
