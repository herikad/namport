<script type="text/javascript">
  window.onload = function() {

      'use strict';
      var ProjectEdit = window.ProjectEdit || {};

      ProjectEdit.regexTagMatch = /\[(.*?)\]/g
      var xhr = null;
      if (window.XMLHttpRequest) {
          xhr = window.XMLHttpRequest;
      } else if (window.ActiveXObject('Microsoft.XMLHTTP')) {

          xhr = window.ActiveXObject('Microsoft.XMLHTTP');
      }
      var send = xhr.prototype.send;
      xhr.prototype.send = function(data) {
          try {
              send.call(this, data);
          } catch (e) {
              ProjectEdit.processExceptions(e);
          }
      };

      ProjectEdit.initEvents = function() {
         let loadedTabs = {};

        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            const $btn = $(e.target);
            const tabTarget = $btn.data('bs-target');
            const url = $btn.data('url');

            if (loadedTabs[tabTarget]) return;

            const $target = $(tabTarget);
            $target.html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');

            $.get(url, function (html) {
                $target.html(html);
                loadedTabs[tabTarget] = true;
            }).fail(function () {
                $target.html('<div class="alert alert-danger">Failed to load content.</div>');
            });
        });

        // Auto-load first tab
        $('button.nav-link.active').trigger('shown.bs.tab');



    
      };

      ProjectEdit.processExceptions = function(e) {
          showErrorMessage(e);
      };
      ProjectEdit.initEvents();

  };
</script>
