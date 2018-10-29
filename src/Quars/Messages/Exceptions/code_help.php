<div class="qrs-error-message block">
<h3>Quars Exception{error_code}: {message}</h3>
{description}</div>
<div class="qrs-ok-message block">
<h3>Que hacer</h3>
{solution}
<div class="code"><pre>
<textarea style="width:100%; height:250px;background:#f0d5de;font-size:13px;">{solution_code}</textarea>
</pre></div>
</div>
<div class="qrs-exception-trace block"><pre>{trace}</pre>

<p><b>Stack</b></p>
<p>{details}</p>
</div>
<style type="text/css">
body{
	background: #F48FB1;
	color:#212121;
}
h3{
	color:#E91E63;	
}
.block{
	border: 1px solid #FAFAFA;
    max-width: 1100px;

    margin: auto;
    margin-top: 8px;
    background: rgba(255, 235, 238, 0.23);
    padding: 15px;
    
    overflow: auto;
    border-radius: 8px;
}
</style>