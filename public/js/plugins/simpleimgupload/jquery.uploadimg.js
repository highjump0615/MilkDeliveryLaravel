(function ($) {
  $.extend({
    simpleimgupload : function (options) {

      // Options + Defaults
      var settings = $.extend({
        input_file: ".img-upload",
        preview_div: ".img-preview",
        file_panel: ".file-panel",
        cancel_bt : ".file-panel span.cancel",
      }, options);

      // Check if FileReader is available
      if (window.File && window.FileList && window.FileReader) {
        if (typeof($(settings.input_file)) !== 'undefined' && $(settings.input_file) !== null) {
          $(settings.input_file).change(function() {
            var files = this.files;

            if (files.length > 0) {
              var file = files[0];
              var reader = new FileReader();

              // Load file
              reader.addEventListener("load",function(event) {
                var loadedFile = event.target;

                // Check format
                if (file.type.match('image')) {
                  // Image
                  $(settings.preview_div).css("background-image", "url("+loadedFile.result+")");
                  $(settings.preview_div).css("background-size", "cover");
                  $(settings.preview_div).css("background-position", "center center");
                  $(settings.preview_div).css("display", "block");
                  $(settings.preview_div).attr("data-attached", "1");
                  $('.hide_after_click').hide();
                } else {
                  alert("This file type is not supported yet.");
                }
              });

              // Read the file
              reader.readAsDataURL(file);
            } else {
              // Clear background
              $(settings.preview_div).css("background-image", "none");
            }
          });


          $(document).on("mouseover", settings.preview_div, function(){
            if($(this).attr("data-attached") == "1")
                $(this).find(settings.file_panel).css("height", "25px");
          });

          $(document).on("mouseleave", settings.preview_div, function(){
            $(this).find(settings.file_panel).css("height", "0px");
          });


          //$(settings.cancel_bt).click(function(){
          $(document).on("click", settings.cancel_bt, function(){
            var preview_div = $(this).closest(settings.preview_div);
            preview_div.hide();
            preview_div.css("background-image", "");
            $(settings.input_file).val("");
          });

        }
      } else {
        alert("You need a browser with file reader support, to use this form properly.");
        return false;
      }
    }
  });
})(jQuery);