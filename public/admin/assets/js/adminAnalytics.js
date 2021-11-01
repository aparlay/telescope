$(document).ready(function() {

    const chartData = $('#chart_data').data('chart');

    var date = new Date();
    date.setDate(date.getDate() - 7);
    document.getElementById('fromDate').value = date.toISOString().slice(0, 10);
    document.getElementById('toDate').value = new Date().toISOString().slice(0, 10); 

    $('#submitDate').click(function(e) {
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var diffDays = parseInt((new Date(toDate) - new Date(fromDate)) / (1000 * 60 * 60 * 24), 10); 

        if (Date.parse(fromDate) > Date.parse(toDate)) {
            $('#alert').html('From date must be less then To date!').addClass('text-danger').show();
            e.preventDefault();
            return false;
        }
        if (diffDays > 30) {
            $('#alert').html('Please select date range between a month').addClass('text-danger').show();
            e.preventDefault();
            return false;
        }

        $.ajax({
            type: 'GET',
            url: $( '#filters' ).attr( 'action' ),
            data: {'fromDate': fromDate, 'toDate': toDate},
            success: function(response)
            {
                chart (response);
            }
        });
    });

    $('#clearFilter').click(function() {
        chart (chartData);
        $('#filters')[0].reset();
        document.getElementById('fromDate').value = date.toISOString().slice(0, 10);
        document.getElementById('toDate').value = new Date().toISOString().slice(0, 10);
    })

    $('#fromDate, #toDate, #clearFilter').click(function () {
        $('#alert').hide();
    });

    chart (chartData);
    function chart (chartData){
        const user_ctx = document.getElementById('user_chart').getContext('2d');
        const user_chart = new Chart(user_ctx, {
            type: 'line',
            data: {
                labels: Object.values(chartData).map(item => item['date']),
                datasets: [
                    {
                        label: 'Registered',
                        borderColor: '#6610f2',
                        data: Object.values(chartData).map(item => item['user']['registered'])
                    },
                    {
                        label: 'Login',
                        borderColor: '#f012be',
                        data: Object.values(chartData).map(item => item['user']['login'])
                    },
                    {
                        label: 'Verified',
                        borderColor: '#01ff70',
                        data: Object.values(chartData).map(item => item['user']['verified'])
                    },
                ]
            },
            options: {
                animations: {
                    enabled: true,
                    easing: 'easeinout'
                },
                scales: {
                    yAxes: [{ticks: {min: 0, max:8, stepSize: 1,}}],
                  }
            }
        });

        const duration_ctx = document.getElementById('user_durations').getContext('2d');
        const user_durations = new Chart(duration_ctx, {
            type: 'line',
            data: {
                labels: Object.values(chartData).map(item => item['date']),
                datasets: [
                    {
                        label: 'Duration',
                        borderColor: '#d81b60',
                        data: Object.values(chartData).map(item => item['user']['duration'])
                    },
                    {
                        label: 'Watched',
                        borderColor: '#3d9970',
                        data: Object.values(chartData).map(item => item['user']['watched'])
                    },
                ]
            },
            options: {
                animations: {
                    enabled: true,
                    easing: 'easeinout'
                }
            }
        });

        const media_ctx = document.getElementById('media_chart').getContext('2d');
        const media_chart = new Chart(media_ctx, {
            type: 'line',
            data: {
                labels: Object.values(chartData).map(item => item['date']),
                datasets: [
                    {
                        label: 'Uploaded',
                        borderColor: '#6610f2',
                        data: Object.values(chartData).map(item => item['media']['uploaded'])
                    },
                    {
                        label: 'Failed',
                        borderColor: '#d81b60',
                        data: Object.values(chartData).map(item => item['media']['failed'])
                    },
                    {
                        label: 'Completed',
                        borderColor: '#ffc107',
                        data: Object.values(chartData).map(item => item['media']['completed'])
                    },
                    {
                        label: 'Confirmed',
                        borderColor: '#007bff',
                        data: Object.values(chartData).map(item => item['media']['confirmed'])
                    },
                    {
                        label: 'Denied',
                        borderColor: '#d81b60',
                        data: Object.values(chartData).map(item => item['media']['denied'])
                    },
                    {
                        label: 'In-Review',
                        borderColor: '#17a2b8',
                        data: Object.values(chartData).map(item => item['media']['in_review'])
                    },
                    {
                        label: 'Deleted',
                        borderColor: '#dc3545',
                        data: Object.values(chartData).map(item => item['media']['deleted'])
                    },
                ]
            },
            options: {
                animations: {
                    enabled: true,
                    easing: 'easeinout'
                },
                scales: {
                    yAxes: [{ticks: {min: 0, max:25, stepSize: 5,}}],
                }
            }
        });

        const visibility_ctx = document.getElementById('media_visibility').getContext('2d');
        const media_visibility = new Chart(visibility_ctx, {
            type: 'line',
            data: {
                labels: Object.values(chartData).map(item => item['date']),
                datasets: [
                    {
                        label: 'Public',
                        borderColor: '#007bff',
                        data: Object.values(chartData).map(item => item['media']['public'])
                    },
                    {
                        label: 'Private',
                        borderColor: '#ffc107',
                        data: Object.values(chartData).map(item => item['media']['private'])
                    },
                ]
            },
            options: {
                animations: {
                    enabled: true,
                    easing: 'easeinout'
                },
                scales: {
                    yAxes: [{ticks: {min: 0, max:25, stepSize: 5,}}],
                }
            }
        });

        const email_ctx = document.getElementById('email_chart').getContext('2d');
        const email_chart = new Chart(email_ctx, {
            type: 'line',
            data: {
                labels: Object.values(chartData).map(item => item['date']),
                datasets: [
                    {
                        label: 'Sent',
                        borderColor: '#6610f2',
                        data: Object.values(chartData).map(item => item['email']['sent'])
                    },
                    {
                        label: 'Opened',
                        borderColor: '#01ff70',
                        data: Object.values(chartData).map(item => item['email']['opened'])
                    },
                    {
                        label: 'Failed',
                        borderColor: '#d81b60',
                        data: Object.values(chartData).map(item => item['email']['failed'])
                    },
                ]
            },
            options: {
                animations: {
                    enabled: true,
                    easing: 'easeinout'
                },
                scales: {
                    yAxes: [{ticks: {min: 0, max:12, stepSize: 2,}}],
                }
            }
        });
    };
})
