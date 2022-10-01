document.addEventListener("DOMContentLoaded", function() {
    if ("undefined" == typeof affCode) var affCode = "c06f665gs";
    (function(b, a) {
        for (var c = 0; c < b.length; c++) a(b[c])
    })(document.querySelectorAll('form[action^="https://ifastnet.com/portal/domainchecker.php"]'), function(b) {
        widget = b.parentElement;
        form = document.createElement("form");
        form.setAttribute("class", "form-inline");
        form.setAttribute("action", "https://www.namesilo.com/domain_results.php?rid=c06f665gs");
        form.setAttribute("target", "_blank");
        form.setAttribute("method", "post");
        form.innerHTML = '<input type="hidden" name="rid" value="c06f665gs"><div class="form-group" style="margin: 20px; 10px;"><input type="text" class="form-control" placeholder="Enter your domain name..." name="domain_search" size="54"></div><div class="form-group" style="margin: 20px; 10px;"></div><button class="btn btn-info" onclick="ga("send", "event", "NameSilo", "DomainSearch");">Check Now</button>';
        col = document.createElement("div");
        col.setAttribute("class", "col-sm-12");
        col.appendChild(form);
        row = document.createElement("div");
        row.setAttribute("class", "row");
        row.appendChild(col);
        widget.innerHTML = "";
        widget.appendChild(row)
    })
});