$(document).ready(function() {
    var flow = new Flow({
        target: window.location.href,
    });
    // Flow.js isn't supported, fall back on a different method
    if(!flow.support) location.href = '/user/index';
    flow.assignBrowse(document.getElementById('media-file'));
    $('.upload-video-button').prop("disabled",true);
    
    $('.flow-drop').show();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var r = new Flow({
        singleFile: true,
        target: $('.flow-drop').data('upload-url'),
        chunkSize: 1024*1024*15,
        query : {
            "_token" : csrfToken
        },
        testChunks: false
    });
    // Flow.js isn't supported, fall back on a different method
    if (!r.support) {
        $('.flow-error').show();
        return ;
    }
    // Show a place for dropping/selecting files
    $('.flow-drop').show();
    r.assignDrop($('.flow-drop')[0], false, false, {accept: 'video/*'});
    r.assignBrowse($('.flow-browse')[0], false, false, {accept: 'video/*'});

    // Handle file add event
    r.on('fileAdded', function(file){
        if (file.size > (1024*1024*1024)) {
            alert("Maximum file size is 1G!");
            return false;
        }
        if (!file.file.type.match('video.*')) {
            alert("Only video files are allowed!");
            return false;
        }
        // Show progress bar
        $('.flow-progress, .flow-list').show();
        // Add the file to the list
        $('.flow-list').html(
            '<p class="flow-file flow-file-'+file.uniqueIdentifier+'">' +
            'Uploading <strong><span class="flow-file-name"></span></strong> ' +
            '<span class="flow-file-size"></span> ' +
            '<strong><span class="flow-file-progress">' +
            '</span></strong> ' +
            '</p>'
        );
        var $self = $('.flow-file-'+file.uniqueIdentifier);
        $self.find('.flow-file-name').text(file.name);
        $self.find('.flow-file-size').text(readablizeBytes(file.size));
    });
    r.on('filesSubmitted', function(file) {
        r.upload();
    });
    r.on('complete', function(){
        // Hide pause/resume when the upload has completed
        $('.flow-progress .progress-resume-link, .flow-progress .progress-pause-link').hide();
        $('.upload-video-button').prop("disabled", false);
    });
    r.on('fileSuccess', function(file,message){
        var $self = $('.flow-file-'+file.uniqueIdentifier);
        // Reflect that the file upload has completed
        $self.find('.flow-file-progress').text('(completed)');
        $self.find('.flow-file-pause, .flow-file-resume').remove();
        var response = JSON.parse(message);
        $('#media-file').val(response.file);
    });
    r.on('fileError', function(file, message){
        // Reflect that the file upload has resulted in error
        $('.flow-file-'+file.uniqueIdentifier+' .flow-file-progress').html('(file could not be uploaded: '+message+')');
    });
    r.on('fileProgress', function(file){
        // Handle progress for both the file and the overall upload
        $('.flow-file-'+file.uniqueIdentifier+' .flow-file-progress')
            .html(Math.floor(file.progress()*100) + '% '
                + readablizeBytes(file.averageSpeed) + '/s '
                + secondsToStr(file.timeRemaining()) + ' remaining') ;
        $('.progress-bar').css({width:Math.floor(r.progress()*100) + '%'});
    });
    r.on('uploadStart', function(){
        // Show pause, hide resume
        $('.flow-progress .progress-resume-link').hide();
        $('.flow-progress .progress-pause-link').show();
    });
    r.on('catchAll', function() {
        console.log.apply(console, arguments);
    });
    window.r = {
        upload: function() {
            r.resume();
        },
        flow: r
    };
});

function readablizeBytes(bytes) {
    var s = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'];
    var e = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, e)).toFixed(2) + " " + s[e];
}

function secondsToStr (temp) {
    function numberEnding (number) {
        return (number > 1) ? 's' : '';
    }
    var years = Math.floor(temp / 31536000);
    if (years) {
        return years + ' year' + numberEnding(years);
    }
    var days = Math.floor((temp %= 31536000) / 86400);
    if (days) {
        return days + ' day' + numberEnding(days);
    }
    var hours = Math.floor((temp %= 86400) / 3600);
    if (hours) {
        return hours + ' hour' + numberEnding(hours);
    }
    var minutes = Math.floor((temp %= 3600) / 60);
    if (minutes) {
        return minutes + ' minute' + numberEnding(minutes);
    }
    var seconds = temp % 60;
    return seconds + ' second' + numberEnding(seconds);
}