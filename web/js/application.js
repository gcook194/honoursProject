//the stuff

//Do the stuff
$(document).ready(function() {

    //Disable the default settings option by default
    //$( "#numberOfTeams1" ).prop("disabled", true);
    //$( "#saveDefaultSettings" ).prop("disabled", true);
    //$( "#roundsOfFixtures" ).prop("disabled", true);

    $('#myTable').DataTable();

    //enable it if the box is checked
    $( "#defaultRules" ).change(function() {
        if (this.checked) {

            //unhide default fields
            $( "#numberOfTeams1" ).prop("disabled", false);
            $( "#roundsOfFixtures" ).prop("disabled", false);
            $( "#saveDefaultSettings" ).prop("disabled", false);

            //hide custom fields
            $( "#league_meta_win_points" ).prop("disabled", true);
            $( "#league_meta_draw_points" ).prop("disabled", true);
            $( "#league_meta_number_of_teams" ).prop("disabled", true);
            $( "#league_meta_sort_col_one" ).prop("disabled", true);
            $( "#league_meta_sort_col_two" ).prop("disabled", true);
            $( "#league_meta_save" ).prop("disabled", true);
        } else {

            //hide default
            $( "#numberOfTeams1" ).prop("disabled", true);
            $( "#roundsOfFixtures" ).prop("disabled", true);
            $( "#saveDefaultSettings" ).prop("disabled", true);

            //Unhude custom
            $( "#league_meta_win_points" ).prop("disabled", false);
            $( "#league_meta_draw_points" ).prop("disabled", false);
            $( "#league_meta_number_of_teams" ).prop("disabled", false);
            $( "#league_meta_sort_col_one" ).prop("disabled", false);
            $( "#league_meta_sort_col_two" ).prop("disabled", false);
            $( "#league_meta_save" ).prop("disabled", false);
        }
    });

    //change the time display box when the range is changed
    $("#fixture_event_timeDisplay").val($("#fixture_event_time").val());

    $("#fixture_event_time").on('input', function() {
        $("#fixture_event_timeDisplay").val($("#fixture_event_time").val());
    });

    $body = $("body");

    var leagueId = $("#leagueId").val();

    if (leagueId !== 'undefined') {
        $.ajax({
            url:'/dashboardCharts',
            type: "POST",
            dataType: "json",
            async: true,
            success: function (data)
            {
                //TOP SCORING TEAMS
                var teams = [];
                var dataset = [];

                for (x in data.table) {
                    teams.push(data.table[x].team);
                    dataset.push(data.table[x].goals);
                }

                var ctx = document.getElementById('myChart').getContext('2d');
                var myBarChart = new Chart(ctx, {
                    type: 'horizontalBar',
                    data: {
                        labels:teams,
                        datasets: [{
                            label: "Goals Scored",
                            backgroundColor: 'rgb(51, 122, 183)',
                            borderColor: 'rgb(51, 122, 183)',
                            data: dataset,
                        }]
                    },
                    options: {
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });

                //TOP SCORING PLAYERS
                var teams2 = [];
                var dataset2 = [];

                for (x in data.scoringPlayers) {
                    teams2.push(data.scoringPlayers[x].player);
                    dataset2.push(data.scoringPlayers[x].goals);
                }

                console.log(dataset);

                var ctx2 = document.getElementById('myChart2').getContext('2d');
                var myBarChart = new Chart(ctx2, {
                    type: 'horizontalBar',
                    data: {
                        labels:teams2,
                        datasets: [{
                            label: "Goals Scored",
                            backgroundColor: 'rgb(51, 122, 183)',
                            borderColor: 'rgb(51, 122, 183)',
                            data: dataset2,
                        }]
                    },
                    options: {
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }
        });
    }

    var id = $("#teamId").val();

    if (id !== 'undefined') {

        $.ajax({
            url:'/teamCharts/' + id,
            type: "POST",
            dataType: "json",
            async: true,
            success: function (data)
            {
                var ctx = document.getElementById('teamResultsPie').getContext('2d');
                var myBarChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels:['wins', 'draws', 'losses'],
                        datasets: [{
                            label: "Goals Scored",
                            backgroundColor: ['rgb(51, 204, 51)', 'rgb(255, 255, 128)', 'rgb(255, 0, 0)'],
                            data: [data.data["wins"], data.data["draws"], data.data["losses"]],
                        }]
                    },
                    options: {

                    }
                });
            }
        });
    }
});