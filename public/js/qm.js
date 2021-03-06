var qm_li;
var qm_lo;
var qm_tt;
var qp = "parentNode";
var qc = "className";
;

function qm_create(sd, v, l)
    {
    if (!l)
        {
        l = 1;
        sd = document.getElementById("qm" + sd);
        sd.onmouseover = function(e)
            {
            x6(e)
            };

        document.onmouseover = x2;
        sd.style.zoom = 1;
        }

    sd.style.zIndex = l;
    var lsp;
    var sp = sd.childNodes;

    for (var i = 0; i < sp.length; i++)
        {
        var b = sp[i];

        if (b.tagName == "A")
            {
            lsp = b;
            b.onmouseover = x0;

            if (l == 1 && v)
                {
                b.style.styleFloat = "none";
                b.style.cssFloat = "none";
                }
            }

        if (sp[i].tagName == "DIV")
            {
            if (window.showHelp && !window.XMLHttpRequest)
                sp[i].insertAdjacentHTML("afterBegin",
                                         "<span style='display:block;font-size:1px;height:0px;width:0px;visibility:hidden;z-index:1000;'></span>");

            x5("qmparent", lsp, 1);
            lsp.cdiv = b;
            b.idiv = lsp;
            new qm_create(b, null, l + 1);
            }
        }
    }

;

function x4(a, b)
    {
    return String.fromCharCode(a.charCodeAt(0) - 1 - (b - (parseInt(b / 4) * 4)));
    }

;

function x2(e)
    {
    if (qm_li && !qm_tt)
        qm_tt = setTimeout("x3()", 500);
    }

;

function x3()
    {
    var a;

    if ((a = qm_li))
        {
        do
            {
            x1(a);
            } while ((a = a[qp]) && !qm_a(a))
        }

    qm_li = null;
    }

;

function qm_a(a)
    {
    if (a[qc].indexOf("qmmc") + 1)
        return 1;
    }

;

function x1(a)
    {
    a.style.display = "none";
    x5("qmactive", a.idiv);
    }


function x0(e)
    {
	try{
		if (qm_tt)
			{
			clearTimeout(qm_tt);
			qm_tt = null;
			}


		var a = this;
		var go = true;

		while ((a = a[qp]) && !qm_a(a))
			{
			if (a == qm_li)
				go = false;
			}



		if (qm_li && go)
			{
			a = this;

			if ((!a.cdiv) || (a.cdiv && a.cdiv != qm_li))
				x1(qm_li);

			a = qm_li;

			while ((a = a[qp]) && !qm_a(a))
				{
				if (a != this[qp])
					x1(a);

				else
					break;
				}
			}

		var b = this;

		if (b.cdiv)
			{
			var aw = b.offsetWidth;
			var ah = b.offsetHeight;
			var ax = b.offsetLeft;
			var ay = b.offsetTop;

			if (qm_a(b[qp]) && b.style.styleFloat != "none" && b.style.cssFloat != "none")
				aw = 0;

			else
				ah = 0;

			b.cdiv.style.left = (ax + aw) + "px";
			b.cdiv.style.top = (ay + ah) + "px";
			x5("qmactive", this, 1);
			b.cdiv.style.display = "block";
			qm_li = b.cdiv;
			}

		else if (b[qp].className != "qmmc")
			qm_li = b[qp];

		x6(e);
	}catch (e){;}
	}
;

function x5(name, b, add)
    {
    var a = b[qc];

    if (add)
        {
        if (a.indexOf(name) == -1)
            b[qc] += (a ? ' ' : '') + name;
        }

    else
        {
        b[qc] = a.replace(" " + name, "");
        b[qc] = b[qc].replace(name, "");
        }
    }

;

function x6(e)
    {
    if (!e)
        e = event;

    e.cancelBubble = true;

    if (e.stopPropagation)
        e.stopPropagation();
    }
;

/**
* Disable Menu
*
* @author Ngo Duc Lien <liennd@gmail.com>
*/
function qm_disable(sd){
    sd = document.getElementById("qm" + sd);
    try{
        sd.disabled=true;
    } catch(e){;}

    var sp = sd.childNodes;
    for (var i = 0; i < sp.length; i++){
        var b = sp[i];
        if (b.tagName == "A"){
            try{//IE
                b.disabled=true;
            } catch(e){;}
            try{//FireFox
                b.className="disabled";
                b.onmouseover = void(0);
            }catch(e){;}
        }
    }
}