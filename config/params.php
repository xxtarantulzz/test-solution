<?php

return [
    'FileInputPluginEvents' => [
        'fileuploaded' => "
            function(event, data, previewId, index) {
                var val = $('.field-' + event.target.id + ' > input').val();
                if(val === 'delete') val = '';
                if(val !== '') val = val + ', ';
                val = val + data.response.path;
                $('.field-' + event.target.id + ' > input').val(val);
            }
        ",
    ]
];
