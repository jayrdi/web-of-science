/* ====================================== */
/* ========= LOAD JSON DATA============== */
/* ====================================== */
/* ========== POPULATE PAGE ============= */
/* ====================================== */


$(document).ready(function() {
	$.ajax({
		type: 'GET',
		url: 'wosData.json',
		dataType: 'json',
		success: function(data) {
			if (data) {
				var len = data.length;
				var txt = "";
				if (len > 0) {
					for (var i = 0; i < len; i++) {
						if (data[i].title[0] && data[i].title[5] && data[i].UID) {
							txt += "<tr><td>" + data[i].title[0] + "</td><td>" + data[i].title[5] + "</td><td>" + data[i].UID + "</td></tr>";
						}
					}
					if (txt != "") {
						$("#table").append(txt).removeClass("hidden");
					}
				}
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert ('error: ' + textStatus + ': ' + errorThrown);
		}
	});
	return false;
});