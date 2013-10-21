(function ($)
{
    $('.indicators').hide();
    
    
    $('.rsvp-link').click(function(){
        $this = $(this);
        rsvpUrl = $this.attr('href');
        id = $this.attr('id').substring(6);
        
        $.ajax({
            url: rsvpUrl,
            type: 'get',
            dataType: 'json',
            beforeSend: function(){
                // $('#indicator-' + id).show();
            },
            error: function(){},
            success: function(){
                // $('#indicator-' + id).hide();
                location.reload();
            }
        });

        return false;
    });
}
( jQuery ));