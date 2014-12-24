
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.

      google.setOnLoadCallback(drawChart);



      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart(id) {

      // Chart One

      if (id)
      {

      // Create the data table.
      var jsonData = jQuery.ajax({
        url: 'function.php',
        dataType: "json",
        type: "POST",
        data: 'id='+id+'&action=formcraft_chart',
        async: false
      }).responseText;

    }
    else
    {

      // Create the data table.
      var jsonData = jQuery.ajax({
        url: 'function.php',
        dataType: "json",
        data: 'action=formcraft_chart',
        async: false
      }).responseText;      

    }



      // Create our data table out of JSON data loaded from server.
      var data = new google.visualization.DataTable(jsonData);

      var d=new Date();

      var month=new Array();
      month[0]="January";
      month[1]="February";
      month[2]="March";
      month[3]="April";
      month[4]="May";
      month[5]="June";
      month[6]="July";
      month[7]="August";
      month[8]="September";
      month[9]="October";
      month[10]="November";
      month[11]="December";

      var options = {
        vAxis: {title: "Number"},
        hAxis: {title: "Day of"},
        seriesType: "bars",
        series: {1: {type: "line"}}
      };
      options.title='Recent Form Views and Submissions';
      options.hAxis.title='Date';


      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }



// Create a New Form
function submit_new_form()
{
  jQuery('.response_ajax').html('processing ...');
  jQuery.ajax({
    url: 'function.php',
    type: "POST",
    dataType: 'json',
    data: 'action=formcraft_add&'+jQuery('#new_form').serialize(),
    success: function (response) {
      if (response.Added)
      {
        jQuery('.response_ajax').html('Added');
        window.location.href = 'builder.php?id='+response.Added;
      }
      else if (response.Error)
      {
        jQuery('.response_ajax').html(response.Error);
      }
    },
    error: function (response) {
      jQuery('.response_ajax').html(response);
    }
  });

}

function setupLabel() 
{

  if (jQuery('.label_check input').length) 
  {
    jQuery('.label_check').each(function()
    { 
      jQuery(this).removeClass('c_on');
    });
    jQuery('.label_check input:checked').each(function()
    { 
      jQuery(this).parent('label').addClass('c_on');
    });                
  };
  if (jQuery('.label_radio input').length) {
    jQuery('.label_radio').each(function(){ 
      jQuery(this).removeClass('r_on');

    });
    jQuery('.label_radio input:checked').each(function(){ 
      jQuery(this).parent('label').addClass('r_on');
    });
  };
};




jQuery(function () {

  jQuery('.import').fileupload({
    dataType: 'json',
    add: function (e, data) 
    {

      var type = data.files[0].name;
      var type = type.split('.');
      var type = type[1];
      if (type!='txt')
      {
        alert('Only .txt files');
        return false;
      }
      data.submit();
      jQuery('#import_field_label').text('wait');

    },
    done: function (e, resp) {

      if(!(resp.result.files[0]).hasOwnProperty('name'))
      {
        jQuery('.import').prop("disabled",false);
        jQuery('#import_field_label').text('Failed');
      }
      else
      {

        jQuery('#import_form').val(resp.result.files[0].name);
        jQuery('.import').prop("disabled",true);
        jQuery('#import_field_label').html('<i class="icon-ok"></i> Done');
        jQuery('#rand_b').trigger('click');
        setupLabel();
      }


    },
    fail: function (e, data){

      jQuery('.import').prop("disabled",false);
      jQuery('#import_field_label').text('Failed');

    }
  });  

});





    // Document Ready
    jQuery(document).ready(function () {

      jQuery("input.rand2").focus(function(){
        event.stopPropagation();
      });

      jQuery('#rand_a').change(function(){
        console.log('a')
        jQuery('#rand_aa').trigger('click');
        setupLabel();

      });




      jQuery('body').on('click', '.delete-row', function() {

       if (confirm('Are you sure you want to delete the form? You can\'t undo this action.')) {



          var this_id = jQuery(this).attr('id');
          jQuery(this).button('loading');
          var id = jQuery(this).parent('td').parent('tr').attr('id');
          jQuery.ajax({
            url: 'function.php',
            type: "POST",
            data: 'action=formcraft_del&id='+id,
            success: function (response) {
              if (response=='Deleted')
              {
                jQuery('#'+this_id).button('complete');
              }
              else
              {
                jQuery('#'+this_id).button('reset');
              }
            },
            error: function (response) {
              alert("There was an error.");
            }
          });

      }

    });



      // Edit Form Name and Description
      jQuery("body").on('click', '.edit_btn', function(event){
        event.stopPropagation();
        jQuery(this).hide();
        jQuery(this).parent().children('.rand').hide();

        var name = jQuery(this).prev('a').html();
        jQuery(this).prev('input.rand2').show();
        jQuery(this).prev('input.rand2').focus();
        jQuery(this).next('a.save_btn').show();
      });

      jQuery('body').on('click','.rand2',function(event){
        event.stopPropagation();
      });

      jQuery("body").on('click', '.save_btn', function(event){
        event.stopPropagation();
        jQuery(this).hide();
        var this_id = jQuery(this).attr('id');
        var id = jQuery(this).attr('id').split('_');
        var val = jQuery(this).parents().children('.rand2').val();

        jQuery.ajax({
          url: 'function.php',
          type: "POST",
          data: 'action=formcraft_name_update&name='+val+'&id='+id[1],
          success: function (response) 
          {
            if (response=='D')
            {
              jQuery('#'+this_id).parent().children('.rand').text(val);
              jQuery('#'+this_id).parent().children('input.rand2').hide();
              jQuery('#'+this_id).parent().children('.rand').show();
              jQuery('#'+this_id).parent().children('.edit_btn').show();

            }
            else
            {
              jQuery('#'+this_id).show();
              jQuery('#'+this_id).parent().children('input.rand2').hide();
              jQuery('#'+this_id).parent().children('.rand').show();
              jQuery('#'+this_id).parent().children('.edit_btn').show();
            }
          },
          error: function (response) 
          {
           jQuery('#'+this_id).show();
         }
       });


      });


  // Update Submissions
  jQuery('.view_mess').click(function(){

    var id = jQuery(this).attr('id').split('_');
    var id2 = jQuery(this).attr('id');

    if (id[0]=='upd')
    {
      jQuery('#view_modal .modal-body').html(jQuery('#upd_text_'+id[1]).html());
      jQuery('#view_modal .myModalLabel').html(jQuery('#upd_name_'+id[1]).html());
      jQuery('#rd_'+id[1]).html('Read');
      jQuery(this).parent().parent().addClass('row_shade');
      jQuery.ajax({
        url: 'function.php',
        type: "POST",
        data: 'action=formcraft_sub_upd&type=upd&id='+id[1],
        success: function (response) {
        },
        error: function (response) {
        }
      });
    }
    else if (id[0]=='del')
    {
      jQuery.ajax({
        url: 'function.php',
        type: "POST",
        data: 'action=formcraft_sub_upd&type=del&id='+id[1],
        success: function (response) {
          if (response=='D')
          {
            jQuery('#'+id2).removeClass('icon-trash');
            jQuery('#'+id2).addClass('icon-ok');
          }
        },
        error: function (response) {
        }
      });
    }
    else if (id[0]=='read')
    {
      jQuery.ajax({
        url: 'function.php',
        type: "POST",
        data: 'action=formcraft_sub_upd&type=read&id='+id[1],
        success: function (response) {
          if (response=='D')
          {
            jQuery('#rd_'+id[1]).html('Unread');
            jQuery('#'+id2).parent().parent().removeClass('row_shade');
          }
        },
        error: function (response) {
        }
      });
    }

  });



  // Delete File From File Manager

  jQuery('.delete_from_manager').click(function(){
    jQuery(this).button('loading');
    var id_this = this.id;
    jQuery.ajax({
      url: 'function.php',
      type: "POST",
      data: 'action=formcraft_delete_file&url='+encodeURIComponent(jQuery(this).attr('data-url')),
      success: function (response) {
        if (response=='Deleted')
        {
          jQuery('#'+id_this).button('complete');
        }
      },
      error: function (response) {
      }
    });
  });

  jQuery('#stats_select').change(function(){
    var val = jQuery(this).val();
    drawChart(val);
  });

  setupLabel();
  jQuery('body').addClass('has-js');
  jQuery('body').on("click",'.label_check, .label_radio' , function(){
    setupLabel();
  });




});