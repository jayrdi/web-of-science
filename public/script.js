
// this will unhide extra search fields when user clicks relevant button
function unhide(divID) {
	var item = document.getElementById(divID);
	if (item) {
		item.className = (item.className == 'hidden')?'unhidden':'hidden';
	}
};