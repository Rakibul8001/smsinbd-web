<div class="modal-body">
  <div class="panel panel-default">
    <div class="panel-heading font-bold">Sender Id Informations</div>
      
      <div class="panel-body">
      
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
              </div>
              <div class="col-md-6">
                <label class="font-bold">Sender ID <span class="text-danger">*</span></label>
                
                <span class="senderidname">
                  <?php echo $smssenderidinfo->sender_name; ?>
                </span>

                <table class="table table-bordered">
                  <thead>
                    <th>Cleint</th>
                    <th class="text-center">Action</th>
                  </thead>
                  <tbody>
                   
                      @foreach($assignedClients as $assignedclient)

                      <tr>
                        <td>{{$assignedclient->getUserOfSenderid->name}}</td>
                        <td class="text-center">
                          @if(Auth::guard('root')->check())
                          <a href="#" data-assign_user_senderid="{{$assignedclient->user}}" data-sms_sender_id="{{$assignedclient->senderid}}" class="btn btn-sm btn-icon btn-pure btn-default senderclientdtl"><i class="icon icon-trash"></i></a>
                          @endif
                        </td>
                      </tr>
                      @endforeach

                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <label for="activeclients col-md-4">Select Client</label>
                <select ui-jq="chosen"  id="activeclients" name="activeclients[]" multiple class="select2Multi form-control w-md col-md-8" style="width: 100%;">
                <?php $clientname = []; $uniqueclient = [];?>    
                @foreach($clients as $client)

                          <option value="{{$client->id}}">{{$client->name}}</option>
                          
                       
                  @endforeach 
                  
                </select>
              </div>
            </div> 
          </div>
          
          
        
      </div>
    </div>
</div>

