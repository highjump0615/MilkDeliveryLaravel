(function ($) {
  $.extend({
    uploadPreview : function (options) {

      // Options + Defaults
      var settings = $.extend({
        cancel_bt: ".cancel",
        file_panel: ".file-panel",
        imageset: ".imageset",
        input_field: ".image-input",
        preview_box: ".image-preview",
        label_field: ".image-label",
        label_default: "Choose File",
        label_selected: "Change File",
        no_label: false
      }, options);

      // Check if FileReader is available
      if (window.File && window.FileList && window.FileReader) {
        if (typeof($(settings.input_field)) !== 'undefined' && $(settings.input_field) !== null) {
          //$(settings.input_field).change(function() {
            $(document).on("change", settings.input_field, function(){
            var files = this.files;

            var imageset = $(settings.imageset);
            var file_input = this;
            var preview_div = $(this).closest(settings.preview_box);

            var previous_background = preview_div.css('background-image');
            if (files.length > 0) {
              var file = files[0];
              var reader = new FileReader();

              // Load file
              reader.addEventListener("load",function(event) {
                var loadedFile = event.target;

                // Check format
                if (file.type.match('image')) {
                  // Image
                  preview_div.css("background-image", "url("+loadedFile.result+")");
                  preview_div.css("background-size", "cover");
                  preview_div.css("background-position", "center center");
                  preview_div.attr("data-attached", "1");

                  //count number of preview_div
                  var count = $(settings.preview_box).length;
                  var end_div=imageset.find(settings.preview_box)[count-1];

                  if(count < 4 && end_div && (end_div.getAttribute("data-attached") == "1")){

                    //check whether right has the preview image
                    imageset.append('<div class="image-preview col-md-2" data-attached="0">\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                        </div>');
                  }

                } else {
                  alert("该文件类型不被支持.");
                }
              });

              if (settings.no_label == false) {
                // Change label
                $(settings.label_field).html(settings.label_selected);
              }
              // Read the file
              reader.readAsDataURL(file);
            } else {
              if (settings.no_label == false) {
                // Change label
                $(settings.label_field).html(settings.label_default);
              }

              // Clear background
              if(previous_background)
                preview_div.css("background-image", previous_background); 
              else
                {
                  preview_div.css("background-image", "none");
                  preview_div.attr("data-attached", "0");
                }
            }
          });

          $(document).on("mouseover", settings.preview_box, function(){
            if($(this).attr("data-attached") == "1")
                $(this).find(settings.file_panel).css("height", "25px");
          });

          $(document).on("mouseleave", settings.preview_box, function(){
            $(this).find(settings.file_panel).css("height", "0px");
          });


          //$(settings.cancel_bt).click(function(){
          $(document).on("click", settings.cancel_bt, function(){
            var preview_div = $(this).closest(settings.preview_box);
            var count = $(settings.preview_box).length;
            var file_input= preview_div.find(".imaeg-upload")[0];
            if(count>1)
            {
              preview_div.remove();
              add_end_preview_div();
            } else {
              preview_div.css("background-image", "");
              preview_div.attr("data-attached", "0");
            }
          });

          function add_end_preview_div(){
            //get last preview div
            //check wheter data attached
            //if yes, add new preview_div
            //if not, return
            var imageset = $(settings.imageset);
            var count = imageset.find(settings.preview_box).length;
            var end_div=imageset.find(settings.preview_box)[count-1];
            if(end_div && (end_div.getAttribute("data-attached") == "1")){
              imageset.append('<div class="image-preview col-md-2" data-attached="0">\
                        <label for="image-upload" class="image-label">选择文件</label>\
                        <input type="file" name="logoimage[]" class="image-upload"/>\
                        <div class="file-panel" style="height: 0px;">\
                            <span class="cancel">删除</span>\
                        </div>\
                        </div>');
              return;
            } else {
              return;
            }
          };

        }
      } else {
        alert("你需要使用文件阅读器支持浏览器，正确使用这种形式.");
        return false;
      }
    }
  });
})(jQuery);