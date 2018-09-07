
import handlebars from 'handlebars';
import layouts from 'handlebars-layouts';
import $ from 'jquery';
export default class template {
	constructor() {
		handlebars.registerHelper(layouts(handlebars));
		this.partials = [];
		this.main = '';
	}
	
	registerPartials() {
		for(let i = 0; i < this.partials.length; i++)	
		    handlebars.registerPartial(this.partials[i].name, $(this.partials[i].file).html());

	}
	
	addPartial(name, path) {
		this.partials.push(
			{
				name: name,
				file: app.ReadFile('src/views/partials/'+path+'.html')
			}
		);
	}
	
	registerMain(path) {
		this.main = app.ReadFile('src/views/'+path+'.html');
	}
	
	compile(element) {
		let temp = handlebars.compile(this.main);
		$('#'+element).append(temp)
	}
}