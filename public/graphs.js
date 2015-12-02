$(document).ready(function() {

    // different colour settings for graphs
    var palette1 = {
        fill: "steelblue",
        hover: "brown"
    };

    var palette2 = {
        fill: "seagreen",
        hover: "darkorange"
    };

    var palette3 = {
        fill: "darkblue",
        hover: "darkmagenta"
    };

    var palette4 = {
        fill: "darkolivegreen",
        hover: "darkseagreen"
    };

    // set title for user defined graph
    var graphTitle = $(".userTitle");
    $(graphTitle).prepend(searchData.from + " to " + searchData.to);
    
    // change graph according to dropdown choice
    var wrapperG = $(".graph_fields_wrap1"); // wrapper for div containing citations graphs
    var wrapperF = $(".graph_fields_wrap2"); // wrapper for div containing funds graphs
    var selector = $("#timeSelect"); // dropdown graph menu ID
    // variables to log subset location in arrays (to use in slice)
    var from1 = 0;
    var to1 = 10;
    var from2 = 0;
    var to2 = 10;
    var from3 = 0;
    var to3 = 10;
    var from4 = 0;
    var to4 = 10;
    var from5 = 0;
    var to5 = 10;
    var selected = "chart2";

    // when the selection is changed in the dropdown menu do:
    $(selector).on("change", function(e) {
        // ignore default action for this event
        e.preventDefault();
        // remove currently displayed graph, 1st child of div (1st graph is 0th)
        $($(wrapperG).children()[1]).remove();
        $($(wrapperF).children()[1]).remove();
        // get value of currently selected
        var selectedVal = $(this).val();
        selected = selectedVal;
        // check value of selected
        // append new graph to wrapper div & run loadGraph to reprocess data
        if (selectedVal == "chart2") {
            $(wrapperG).append("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>" + searchData.from + " to " + searchData.to + "</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>1-10</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart2 well bs-component'></div></div>").loadGraph(topCitedYears.slice(0,10), selectedVal, palette2);
            $(wrapperF).append("<div class='col-lg-6'><h3 class='titles'>Ranked Awarded Funds (£millions)</h3><h4 class='titles'>" + searchData.from + " to " + searchData.to + ", UK only</h4><div class='chart8 well bs-component'></div></div>").loadGraph(topFundedYears.slice(0,10), "chart8", palette4);
        } else if (selectedVal == "chart4") {
            $(wrapperG).append("<div class='col-lg-6' id='tenYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 10 years)</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>1-10</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart4 well bs-component'></div></div>").loadGraph(topCitedTen.slice(0,10), selectedVal, palette2);
            $(wrapperF).append("<div class='col-lg-6'><h3 class='titles'>Ranked Awarded Funds (£millions)</h3><h4 class='titles'>(Last 10 years, UK only)</h4><div class='chart9 well bs-component'></div></div>").loadGraph(topFundedTen.slice(0,10), "chart9", palette4);
        } else if (selectedVal == "chart5") {
            $(wrapperG).append("<div class='col-lg-6' id='fiveYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 5 years)</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>1-10</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart5 well bs-component'></div></div>").loadGraph(topCitedFive.slice(0,10), selectedVal, palette2);
            $(wrapperF).append("<div class='col-lg-6'><h3 class='titles'>Ranked Awarded Funds (£millions)</h3><h4 class='titles'>(Last 5 years, UK only)</h4><div class='chart10 well bs-component'></div></div>").loadGraph(topFundedFive.slice(0,10), "chart10", palette4);
        } else if (selectedVal == "chart6") {
            // if the graph starts with less than 10 values, disable the 'next' button
            if ((topCitedTwo.length) < 10) {
                $(wrapperG).append("<div class='col-lg-6' id='twoYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 2 years)</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>1-10</button><button class='pager' id='next2' type='button' disabled>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart6 well bs-component'></div></div>").loadGraph(topCitedTwo.slice(0,10), selectedVal, palette2);
            } else {
                $(wrapperG).append("<div class='col-lg-6' id='twoYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 2 years)</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>1-10</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart6 well bs-component'></div></div>").loadGraph(topCitedTwo.slice(0,10), selectedVal, palette2);
            }
            $(wrapperF).append("<div class='col-lg-6'><h3 class='titles'>Ranked Awarded Funds (£millions)</h3><h4 class='titles'>(Last 2 years, UK only)</h4><div class='chart11 well bs-component'></div></div>").loadGraph(topFundedTwo.slice(0,10), "chart11", palette4);
        }

    });

    /*****************************************/
    /***********  PAGINATION *****************/
    /*****************************************/
    // ALL TIME CITED, chart1
    // next author set
    $(wrapperG).on("click", "#next1", function (e) {
        // ignore default action for this event
        e.preventDefault();
        // shift pointers up 10 for next subset of array
        from1 += 10;
        to1 += 10;
        // check if at end of data
        if (to1 > (topCited.length)) {
            // remove currently displayed graph, 0th child of div
            $($(wrapperG).children()[0]).remove();
            // load new graph before other graph (0th child of div)
            $(wrapperG).prepend("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>All time (from 1970)</h4><button class='pager' id='previous1' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from1+1) + " - " + to1 + "</button><button class='pager' id='next1' type='button' disabled>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart1 well bs-component'></div></div>").loadGraph((topCited.slice(from1,to1)), "chart1", palette1);
        } else {
            // remove currently displayed graph, 0th child of div
            $($(wrapperG).children()[0]).remove();
            // load new graph before other graph (0th child of div)
            $(wrapperG).prepend("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>All time (from 1970)</h4><button class='pager' id='previous1' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from1+1) + " - " + to1 + "</button><button class='pager' id='next1' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart1 well bs-component'></div></div>").loadGraph((topCited.slice(from1,to1)), "chart1", palette1);
        }
    });

    // all time cited, previous author set
    $(wrapperG).on("click", "#previous1", function (e) {
        // ignore default action for this event
        e.preventDefault();
        // shift pointers down 10 for previous subset of array
        from1 -= 10;
        to1 -= 10;
        // check if at start of data
        if (from1 == 0) {
            // remove currently displayed graph, 0th child of div
            $($(wrapperG).children()[0]).remove();
            // load new graph before other graph (0th child of div)
            $(wrapperG).prepend("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>All time (from 1970)</h4><button class='pager' id='previous1' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from1+1) + " - " + to1 + "</button><button class='pager' id='next1' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart1 well bs-component'></div></div>").loadGraph((topCited.slice(from1,to1)), "chart1", palette1);
        } else {
            // remove currently displayed graph, 0th child of div
            $($(wrapperG).children()[0]).remove();
            // load new graph before other graph (0th child of div)
            $(wrapperG).prepend("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>All time (from 1970)</h4><button class='pager' id='previous1' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from1+1) + " - " + to1 + "</button><button class='pager' id='next1' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart1 well bs-component'></div></div>").loadGraph((topCited.slice(from1,to1)), "chart1", palette1);
        }
    });

    // OPTIONS CITED, charts 2, 4, 5 & 6
    // optional cited, next author set
    $(wrapperG).on("click", "#next2", function (e) {
        // ignore default action for this event
        e.preventDefault();
        // remove currently displayed graph, 1st child of div (1st graph is 0th)
        $($(wrapperG).children()[1]).remove();
        // check selectedVal to see which graph to append
        switch(selected) {
            case ("chart2"):
                // shift pointers up 10 for next subset of array
                from2 += 10;
                to2 += 10;
                // check if at end of data
                if (to2 >= (topCitedYears.length)) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>" + searchData.from + " to " + searchData.to + "</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from2+1) + " - " + to2 + "</button><button class='pager' id='next2' type='button' disabled>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart2 well bs-component'></div></div>").loadGraph((topCitedYears.slice(from2,to2)), "chart2", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>" + searchData.from + " to " + searchData.to + "</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from2+1) + " - " + to2 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart2 well bs-component'></div></div>").loadGraph((topCitedYears.slice(from2,to2)), "chart2", palette2);
                }
                break;
            case ("chart4"):
                // shift pointers up 10 for next subset of array
                from3 += 10;
                to3 += 10;
                // check if at end of data
                if (to3 >= (topCitedTen.length)) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6' id='tenYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 10 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from3+1) + " - " + to3 + "</button><button class='pager' id='next2' type='button' disabled>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart4 well bs-component'></div></div>").loadGraph((topCitedTen.slice(from3,to3)), "chart4", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6' id='tenYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 10 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from3+1) + " - " + to3 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart4 well bs-component'></div></div>").loadGraph((topCitedTen.slice(from3,to3)), "chart4", palette2);
                }
                break;
            case ("chart5"):
                // shift pointers up 10 for next subset of array
                from4 += 10;
                to4 += 10;
                // check if at end of data
                if (to4 >= (topCitedFive.length)) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6' id='fiveYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 5 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from4+1) + " - " + to4 + "</button><button class='pager' id='next2' type='button' disabled>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart5 well bs-component'></div></div>").loadGraph((topCitedFive.slice(from4,to4)), "chart5", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6' id='fiveYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 5 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from4+1) + " - " + to4 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart5 well bs-component'></div></div>").loadGraph((topCitedFive.slice(from4,to4)), "chart5", palette2);
                }
                break;
            case ("chart6"):
                // shift pointers up 10 for next subset of array
                from5 += 10;
                to5 += 10;
                // check if at end of data
                if (to5 >= (topCitedTwo.length)) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6' id='twoYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 2 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from5+1) + " - " + to5 + "</button><button class='pager' id='next2' type='button' disabled>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart6 well bs-component'></div></div>").loadGraph((topCitedTwo.slice(from5,to5)), "chart6", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6' id='twoYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 2 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from5+1) + " - " + to5 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart6 well bs-component'></div></div>").loadGraph((topCitedTwo.slice(from5,to5)), "chart6", palette2);
                }
                break;
            default:
                console.log("ERROR!");
                break;
        }
    });

    // // optional cited, previous author set
    $(wrapperG).on("click", "#previous2", function (e) {
        // ignore default action for this event
        e.preventDefault();
        // remove currently displayed graph, 1st child of div (1st graph is 0th)
        $($(wrapperG).children()[1]).remove();
        // check selectedVal to see which graph to append
        switch(selected) {
            case ("chart2"):
                // shift pointers down 10 for previous subset of array
                from2 -= 10;
                to2 -= 10;
                // check if at start of data
                if (from2 == 0) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>" + searchData.from + " to " + searchData.to + "</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from2+1) + " - " + to2 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart2 well bs-component'></div></div>").loadGraph((topCitedYears.slice(from2,to2)), "chart2", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>" + searchData.from + " to " + searchData.to + "</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from2+1) + " - " + to2 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart2 well bs-component'></div></div>").loadGraph((topCitedYears.slice(from2,to2)), "chart2", palette2);
                }
                break;
            case ("chart4"):
                // shift pointers down 10 for previous subset of array
                from3 -= 10;
                to3 -= 10;
                // check if at start of data
                if (from3 == 0) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6' id='tenYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 10 years)</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from3+1) + " - " + to3 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart4 well bs-component'></div></div>").loadGraph((topCitedTen.slice(from3,to3)), "chart4", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6' id='tenYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 10 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from3+1) + " - " + to3 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart4 well bs-component'></div></div>").loadGraph((topCitedTen.slice(from3,to3)), "chart4", palette2);
                }
                break;
            case ("chart5"):
                // shift pointers down 10 for previous subset of array
                from4 -= 10;
                to4 -= 10;
                // check if at start of data
                if (from4 == 0) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6' id='fiveYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 5 years)</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from4+1) + " - " + to4 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart5 well bs-component'></div></div>").loadGraph((topCitedFive.slice(from4,to4)), "chart5", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6' id='fiveYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 5 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from4+1) + " - " + to4 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart5 well bs-component'></div></div>").loadGraph((topCitedFive.slice(from4,to4)), "chart5", palette2);
                }
                break;
            case ("chart6"):
                // shift pointers down 10 for previous subset of array
                from5 -= 10;
                to5 -= 10;
                // check if at start of data
                if (from5 == 0) {
                    // load new graph after other graph (1st child of div)
                    $(wrapperG).append("<div class='col-lg-6' id='twoYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 2 years)</h4><button class='pager' id='previous2' type='button' disabled><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from5+1) + " - " + to5 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart6 well bs-component'></div></div>").loadGraph((topCitedTwo.slice(from5,to5)), "chart6", palette2);
                } else {
                    $(wrapperG).append("<div class='col-lg-6' id='twoYear'><h3 class='titles'>Ranked Author Citations</h3><h4 class='titles'>(Last 2 years)</h4><button class='pager' id='previous2' type='button'><span class='glyphicon glyphicon-chevron-left'></span> previous</button><button class='pager indexer' type='button' disabled>" + (from5+1) + " - " + to5 + "</button><button class='pager' id='next2' type='button'>next <span class='glyphicon glyphicon-chevron-right'></span></button><div class='chart6 well bs-component'></div></div>").loadGraph((topCitedTwo.slice(from5,to5)), "chart6", palette2);
                }
                break;
            default:
                console.log("ERROR!");
                break;
        }
    });

    // create arrays to store various chart names
    var citedCharts = [
                          "chart1",
                          "chart2",
                          "chart4",
                          "chart5",
                          "chart6"
                      ];
    var fundedCharts = [
                          "chart7",
                          "chart8",
                          "chart9",
                          "chart10",
                          "chart11"
                      ];

    // Immediately Invoked Function Expression: allows '$' to work with any other plugins
    (function ($) {
        // add function to '$.fn' object (contains all jQuery object methods)
        $.fn.loadGraph = function(graphData, graphSelect, graphColour) {

            // ERROR CHECKING if no data, display message
            if (graphData.length == 0) {
                $("." + graphSelect + "").append('<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">NO DATA</h3></div><div class="panel-body">There are no data available for this query</div></div>');
                // break out of function
                return false;
            };

            // establish some margins for the graph area to avoid overlap with other HTML elements
            var margin = {
                            top: 30,
                            right: 0,
                            bottom: 180,
                            left: 90
                         };

            // initiate variables for max width and height of canvas for chart, determine largest citation value for domain and scaling
            // width, 10 bars at 75px each plus 3px padding & space for axis labelling
            var height = 300;
            var width = 510;

            // set a value for the number of authors in the dataset * width of one bar (420)
            var numAuthor = ((graphData.length) * 42);

            // maximum height of y axis is maximum number of citations/values (first element)
            if (graphSelect == "chart3") {
                var citedMaxY = graphData[0].values;
            } else if (fundedCharts.indexOf(graphSelect) != -1) {
                var citedMaxY = graphData[0].funds;
            } else {
                var citedMaxY = graphData[0].citations;
            }

            // set scale to alter data set so that it fits well in the canvas space
            // map the domain (actual data range) to the range (size of canvas)
            var citedLinearScale = d3.scale.linear()
                                     // 0 -> largest citations value
                                     .domain([0, citedMaxY])
                                     // 0 -> 500
                                     .range([0, height]);

            // create canvas for citations (user defined) chart
            var svgContainer = d3.select("." + graphSelect).append("svg")
                                                           .attr("width", width)
                                                           // max size from data set plus 20px margin
                                                           .attr("height", height + margin.bottom);

            // create an SVG Grouped Element (<g>) to contain all the bars of the graph
            var barGroup = svgContainer.append("g")
                                       .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            // bind the data to SVG Rectangle elements, set colours
            var citedBar = barGroup.selectAll("rect")
                                   .data(graphData) 
                                   .enter()
                                   .append("rect")
                                   .attr("fill", graphColour.fill)
                                   // highlight each bar as you hover over it
                                   .on("mouseover", function () {
                                       d3.select(this)
                                         .attr("fill", graphColour.hover);    
                                   })
                                   // transition to remove highlight from bar
                                   .on("mouseout", function() {
                                       d3.select(this)
                                         .transition()
                                         .duration(250)
                                         .attr("fill", graphColour.fill);
                                   })
                                   // when click on bar, performs Google search according to author name/GtR ID
                                   .on("click", function (d) {
                                       switch (graphSelect) {
                                           case "chart1":
                                           case "chart2":
                                           case "chart3":
                                           case "chart4":
                                           case "chart5":
                                           case "chart6":
                                               // variable stores url for google and adds author name relevant to bar that was clicked
                                               var url = "https://www.google.co.uk/#q=" + d.author;
                                               // add an href html element with the url attached
                                               $(location).attr("href", url);
                                               window.location = url;
                                               break;
                                           default:
                                               // variable stores url for google and adds ID relevant to bar that was clicked
                                               var url = "https://www.google.co.uk/#q=" + d.personID + " " + d.author;
                                               // add an href html element with the url attached
                                               $(location).attr("href", url);
                                               window.location = url;
                                               break;
                                       }
                                   });

            // set variable to store bar width + padding
            var barWidth = 42;

            // set attributes for the bars
            var citedRect = citedBar.attr("width", 40)
                                    // set bar height by value of citations
                                    .attr("height", 0)
                                    // index * 78 will move each bar (width, 75px) one bar width along and leave 3px padding
                                    .attr("x", function (d, i) {
                                        return i * barWidth;
                                    })
                                    // this is determined from the top left corner so to get the bar at the bottom, take the bar height from the canvas height
                                    .attr("y", function (d) {
                                        return height;
                                    })
                                    // animated bars
                                    .transition()
                                        .delay(function (d, i) {
                                            return i * 100;
                                        })
                                        .duration(200)
                                        .attr("y", function (d) {
                                            if (graphSelect == "chart3") {
                                                return height - citedLinearScale(d.values);
                                            } else if (fundedCharts.indexOf(graphSelect) != -1) {
                                                return height - citedLinearScale(d.funds);
                                            } else {
                                                return height - citedLinearScale(d.citations);
                                            }
                                        })
                                        .attr("height", function (d) {
                                            if (graphSelect == "chart3") {
                                                return citedLinearScale(d.values);
                                            } else if (fundedCharts.indexOf(graphSelect) != -1) {
                                                return citedLinearScale(d.funds);
                                            } else {
                                                return citedLinearScale(d.citations);
                                            }
                                        });

            // bind the data to SVG Text elements
            var citedText = barGroup.selectAll("text")
                                    .data(graphData)
                                    .enter()
                                    .append("text");

            // set attributes for the text on bars (citation values)
            var citedLabels = citedText.attr("x", function (d, i) {
                                           return (barWidth * i) + 20; // sets to halfway between each bar horizontally
                                       })
                                       .attr("y", function (d) {
                                           if (graphSelect == "chart3") {
                                               return height - (citedLinearScale(d.values)) -5;
                                           } else if (fundedCharts.indexOf(graphSelect) != -1) {
                                               return height - (citedLinearScale(d.funds)) -5;   
                                           } else {
                                               return height - (citedLinearScale(d.citations)) - 5; // sets to top of each bar -3 to sit just above bar
                                           }
                                       })
                                       .text(function (d) {
                                           if (graphSelect == "chart3") {
                                               return d.values;
                                           } else {
                                               return d.citations; // value to display, citations value (number)  
                                           }
                                       })
                                       .style("text-anchor", "middle")
                                       .attr("font-family", "Raleway")
                                       .attr("font-size", "18px")
                                       .attr("font-weight", "900")
                                       .attr("fill", graphColour.fill);

            //***** SCALES *****//

            // create a scale for the horizontal axis
            // creates a new ordinal scale (allows strings in domain) with empty domain and range
            var xScale = d3.scale.ordinal()
                                 // set input domain to specified values from data
                                 .domain(graphData.map(function (d) {
                                     return d.author;
                                 }))
                                 // sets output range to fit number of authors, and therefore bars (0-780)
                                 .rangeBands([0, numAuthor]);

            // create a scale for the vertical axis
            var yScale = d3.scale.linear()
                                 .domain([0, citedMaxY])
                                 .range([height, 0]);

            //***** AXES *****//                     

            // define x-axes
            var xAxis = d3.svg.axis()
                              .scale(xScale)
                              .orient("bottom")
                              .ticks(10);

            // define y-axes
            var yAxis = d3.svg.axis()
                          .scale(yScale)
                          .orient("left")
                          .ticks(10);

            //***** BAR & AXIS LABELLING *****//

            // if this calculation is done in "translate" below, it concatenates instead of adding values
            var translateY = height + margin.top;

            // create a tooltip
            var tooltip = d3.select("body")
                            .append("div")
                            .style("position", "absolute")
                            .style("z-index", "10")
                            .style("visibility", "hidden")
                            .style("color", "white")
                            .style("padding", "8px")
                            .style("background-color", "rgba(0,0,0,0.75)")
                            .style("border-radius", "6px")
                            .style("font", "12px sans-serif")
                            .text("tooltip");

            // create x-axes
            svgContainer.append("g")
                        .attr("class", "axis")
                        .attr("transform", "translate(" + margin.left + "," + translateY + ")")
                        .call(xAxis)
                         // select author names
                        .selectAll("text")
                        .attr("font-family", "Lora")
                        .style("text-anchor", "end")
                        // spacing
                        .attr("dx", "-.8em")
                        .attr("dy", ".15em")
                        .attr("font-size", "14px")
                        // display author name when hover over author name
                        .on("mouseover", function (d, i) {
                          switch (graphSelect) {
                            case "chart1":
                            case "chart2":
                            case "chart4":
                            case "chart5":
                            case "chart6":
                              tooltip.text(graphData[i]['country']);
                              tooltip.style("visibility", "visible");
                              break;
                            default:
                              break;
                          }
                        })
                        // when move mouse around circle, keep tooltip affixed to same place relative to pointer
                        .on("mousemove", function (d) {
                          return tooltip.style("top", (d3.event.pageY-10)+"px").style("left", (d3.event.pageX+10))
                        })
                        .on("mouseout", function (d) {
                          return tooltip.style("visibility", "hidden");
                        })
                        // rotate text as too long to display horizontally
                        .attr("transform", function (d) {
                            return "rotate(-45)";
                        });

            // create y-axes
            svgContainer.append("g")
                        .attr("class", "axis")
                        .attr("transform", "translate(" + margin.left  + "," + margin.top + ")")
                        .call(yAxis)
                        // append a title to the y-axis
                        .append("text")
                        .attr("transform", "rotate(-90)")
                        .attr("y", -70)
                        .attr("x", -50)
                        .style("text-anchor", "end")
                        .attr("fill", "#000")
                        .attr("font-family", "Lora")
                        .attr("font-size", "20px")

            // allows 'chaining', i.e. link multiple actions to single selector (e.g. '.attr' followed by '.css')
            // return this;
        };
    } (jQuery));
        
    // bubble chart
    (function ($) {
        $.fn.loadBubbles = function(graphData, graphSelect) {

            var width = 580;
            var height = 440;

            // create new pack layout as bubble
            var bubble = d3.layout.pack()
                           .sort(null)
                           .value(function (d) {
                               return d.weight;  
                           })
                           .size([width, height]);
                           // .padding(3);

            // select chart3 div and append an svg canvas to draw the circles onto
            var canvas = d3.select(".chart3").append("svg")
                                             .attr("width", width)
                                             .attr("height", height)
                                             .append("g");

            // create a tooltip
            var tooltip = d3.select("body")
                            .append("div")
                            .style("position", "absolute")
                            .style("z-index", "10")
                            .style("visibility", "hidden")
                            .style("color", "white")
                            .style("padding", "8px")
                            .style("background-color", "rgba(0,0,0,0.75)")
                            .style("border-radius", "6px")
                            .style("font", "12px sans-serif")
                            .text("tooltip");

            // parse data for use with bubble chart
            jsonData = JSON.parse(topValued);

            // run bubble returning array of nodes associated with data
            // will output array of data with computed position of all nodes
            // and populates some data for each node:
            // depth, starting at 0 for root, x coord, y coord, radius
            var nodes = bubble.nodes(jsonData)
                              // filter out the outer circle (root node)
                              .filter(function (d) {
                                  return !d.children; 
                              });

            var node = canvas.selectAll(".node")
                             .data(nodes)
                             .enter()
                             .append("g")
                             // give nodes a class name for referencing
                             .attr("class", "node")
                             .attr("transform", function (d) {
                              return "translate(" + d.x + "," + d.y + ")";
                             });

            // append the circle graphic for each node
            node.append("circle")
              // radius from data
                .attr("r", function (d) {
                    return d.r; 
                })
                // colour circles according to associated values
                .attr("fill", function (d, i) {
                  if (i == 0) {
                    return "#5c0000";
                  }
                  else if (i == 1) {
                    return "#6b0000";
                  }
                  else if (i == 2) {
                    return "#7a0000";
                  }
                  else if (i == 3) {
                    return "#8a0000";
                  }
                  else if (i == 4) {
                    return "#990000";
                  }
                  else if (i == 5) {
                    return "#a31919";
                  }
                  else if (i == 6) {
                    return "#ad3333";
                  }
                  else if (i == 7) {
                    return "#b84d4d";
                  }
                  else if (i == 8) {
                    return "#c26666";
                  }
                  else {
                    return "#cc8080";
                  }
                })
                // set stroke for circles
                // .attr("stroke", "#000")
                // .attr("stroke-width", 5)
                // display author name when hover over circle
                .on("mouseover", function (d) {
                  tooltip.text(d.author);
                  tooltip.style("visibility", "visible");
                })
                // when move mouse around circle, keep tooltip affixed to same place relative to pointer
                .on("mousemove", function (d) {
                  return tooltip.style("top", (d3.event.pageY-10)+"px").style("left", (d3.event.pageX+10))
                })
                .on("mouseout", function (d) {
                  return tooltip.style("visibility", "hidden");
                })
                // when click on bar, performs Google search according to author name
                .on("click", function (d) {
                    // variable stores url for google and adds author name relevant to bar that was clicked
                    var url = "https://www.google.co.uk/#q=" + d.author;
                    // add an href html element with the url attached
                    $(location).attr("href", url);
                    window.location = url;
                });

            // add author name to identify nodes
            node.append("text")
                .style("text-anchor", "middle")
                .style("font-family", "'Raleway', sans-serif")
                .style("font-weight", "bold")
                .style("font-size", "24px")
                .style("fill", "#000")
                .attr("dy", ".3em");
                //.text(function (d) {
                    // return d.children ? "" : d.values;
              // });
        };
    } (jQuery));

    // load initial graphs to page, 1st 10 authors
    $(".chart1").loadGraph(topCited.slice(0,10), "chart1", palette1);
    $(".chart2").loadGraph(topCitedYears.slice(0,10), "chart2", palette2);
    $(".chart3").loadBubbles(topValued, "chart3");
    $(".chart7").loadGraph(topFunded.slice(0,10), "chart7", palette3);
    $(".chart8").loadGraph(topFundedYears.slice(0,10), "chart8", palette4);

});
