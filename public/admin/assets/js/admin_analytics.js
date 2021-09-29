$(document).ready(function() {

    const chartData = $('#chart_data').data('chart');

    const user_ctx = document.getElementById('user_chart').getContext('2d');
    console.log(chartData);
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
            }
        }
    });
})
