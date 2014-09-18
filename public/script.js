// this will unhide extra search fields when user clicks relevant button
function unhide(divID) {
	var item = document.getElementById(divID);
	if (item) {
		item.className = (item.className == 'hidden')?'unhidden':'hidden';
	}
}

function display() {
	document.write("ACCESS, BITCHES!");
	/* $.ajax({
		type: "GET",
		url: "wos.php",
		dataType: "json",
		success: function(data) {
			document.write(data);
		}
	}) */
}
