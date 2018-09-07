const fs = require('fs');
const handlebars = require('handlebars');
const layouts = require('handlebars-layouts');
const $ = require('jquery');

module.exports = class template {
	constructor(main, ext = '.html') {
		handlebars.registerHelper(layouts(handlebars));
		this.partials = [];
		this.main = main;
		this.ext = ext;
		
		if(main != undefined)
		    this.registerMain(this.main);
	}
	
	registerPartials() {
		for(let i = 0; i < this.partials.length; i++)	
		    handlebars.registerPartial(this.partials[i].name, $(this.partials[i].file).html());

	}
	
	addPartial(name, path) {
		this.partials.push(
			{
				name: name,
				file: fs.readFileSync(path+this.ext, 'utf8')
			}
		);
	}
	
	registerMain(path) {
		this.main = fs.readFileSync(path+this.ext, 'utf8');
	}
	
	compile(data) {
	
		let temp = handlebars.compile(this.main, data);
		return temp(data);
	}
}