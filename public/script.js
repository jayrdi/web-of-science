$(document).ready(function() {
	// this will unhide extra search fields when user clicks relevant button
	function unhide(divID) {
		var item = document.getElementById(divID);
		if (item) {
			item.className = (item.className == 'hidden')?'unhidden':'hidden';
		}
	};

	function display() {
		document.write("ACCESS, BITCHES!");

	};

	function d3bubbles(dataset) {

		// set diamter of canvas to display bubbles onto
		var diameter = 960;

		// create new pack layout set to variable 'bubble'
		var bubble = d3.layout.pack()
					   //no sorting, size allocated 906 x 960 with padding 1.5px
					   .sort(null)
					   .size([diameter, diameter])

		// select HTML block where the visualisation will be displayed and append an SVG canvas to 'draw' the circles onto
		var canvas = d3.select('#').append('svg')
								   .attr('width', diameter)
								   .attr('height', diameter)
								   // standard HTML element to display SVG
								   .append('g')
	}
)};