<div class="col-sm-12 col-xs-12">
    @include('smsview.common.user-head-title')
    
    <div class="col-sm-6 col-xs-12">
    <small class="text-muted">Balance Recharge</small>
    
    @if(Auth::user()->reseller_id == 0)
    
<div style="border: 3px dashed#ff7600;background: #feead0;padding: 7px;margin-top:7px;">
<p>ব্যালান্স রিচার্জ করার জন্য নিচে দেয়া নম্বরে টাকা পাঠিয়ে দিন, তারপর আপনার Mobile Number,  Amount, TransactionID পাঠিয়ে দিন 01777333677 এই নম্বরে  </p>
<p></p>
<p>bKash 01777333675 (merchant)</p>
<p>bKash 01777333677 (personal)</p>
<p>Nagad 01777333678 (personal)</p> 
</div>


</div>



<div class="col-sm-6 col-xs-12"style="text-align: left;">
<small class="text-muted"style="padding: 2px; margin:7px;">Masking Registration</small>
        
<div style="border: 3px dashed#ff7600;background: #f3fffb;padding: 7px;margin:7px;">
<p>মাস্কিং আইডি রেজিস্ট্রেশন করার জন্য নিচে দেয়া প্রথম দুটি ফাইল ডাউনলোড করুন এরপর আপনার অফিসিয়াল প্যাডে প্রিন্ট করে সিল ও সাক্ষর দিয়ে পাঠিয়ে দিন smsinbd@gmail.com এই ইমেইল এ  </p>
<p></p>
<p><a href="http://login.smsinbd.com/downloads/AutLtrBlankGP" style="padding: 17px;"><i class="fa fa-download"></i>  Download Masking Application for GP</a> </p> 
<p><a href="http://login.smsinbd.com/downloads/AutLtrBlank" style="padding: 17px;"><i class="fa fa-download"></i>  Download Masking Application for Other Operators</a> </p> 
<p><a href="http://login.smsinbd.com/downloads/BTRC-Form-20200001" style="padding: 17px;"><i class="fa fa-download"></i>  Download BTRC Content Approval Form</a>  </p>

</div>

@endif


</div>