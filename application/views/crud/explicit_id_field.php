<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<input type="text" maxlength="100" value="<?php echo $value; ?>" name="explicitId" id="field-explicitId">
<input id="generate-explicit-id-button" type="button" title="<?php echo $button_title; ?>" value="Generieren">
<script type="text/javascript">
$(function() {
    $('#generate-explicit-id-button').button()
        .click(function() {
            // get the values:
            var title = $('#field-title').val(),
                authors = $('#field-authors').val(),
                year = $('#field-year').val(), 
                explicitId = '';

            // Add lastname of author(s)
            var authorsArray = authors.split("/");
            for (var i = 0; i < authorsArray.length; i++) {
                // remove the first name
                var authorName = authorsArray[i];
                authorsArray[i] = $.trim(authorName.substring(0, authorName.indexOf(",")));
            }
            // combine lastnames of the authors:
            explicitId += authorsArray.join("_");
            
            // Add year
            // Extract first publication, if given
            year = year.replace(/\[\d+\]/, '');
            // Delete edition number, if given
            year = year.replace(/<sup>\d+<\/sup>/, '');
            
            explicitId += "_" + $.trim(year);
            
            $('#field-explicitId').val(explicitId);
        })
    });
</script>
<?php
/* End of file explicit_id_field.php */
/* Location: ./application/views/crud/explicit_id_field.php */
?>
