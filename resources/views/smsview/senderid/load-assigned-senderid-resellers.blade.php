<div class="modal-body">
  <div class="panel panel-default">
    <div class="panel-heading font-bold">Sender Id Informations (Resellers)</div>
      
      <div class="panel-body">
      
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
              </div>
              <div class="col-md-6">
                <label class="font-bold">Sender ID <span class="text-danger">*</span></label>
                
                <span class="senderidname">
                  <?php echo $senderidInfo->sender_name; ?>
                </span>

                <table class="table table-bordered">
                  <thead>
                    <th>Reseller</th>
                    <th class="text-center">Action</th>
                  </thead>
                  <tbody>
                   
                      @foreach($assignedResellers as $assignedReseller)

                      <tr>
                        <td>{{$assignedReseller->getResellerOfSenderid->name}}</td>
                        <td class="text-center">
                          @if(Auth::guard('root')->check())
                          <a href="#" data-senderid_reseller="{{$assignedReseller->reseller}}" data-sms_sender_id="{{$assignedReseller->senderid}}" class="btn btn-sm btn-icon btn-pure btn-default senderidResellerDelete"><i class="icon icon-trash"></i></a>
                          @endif
                        </td>
                      </tr>
                      @endforeach

                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <label for="activeResellers col-md-4">Select Reseller</label>
                <select ui-jq="chosen"  id="activeResellers" name="activeResellers[]" multiple class="form-control w-md col-md-8" style="width: 100%;">  
                @foreach($resellers as $reseller)
                    <option value="{{$reseller->id}}">{{$reseller->name}}</option>
                @endforeach 
                  
                </select>
              </div>
            </div> 
          </div>
          
          
        
      </div>
    </div>
</div>

