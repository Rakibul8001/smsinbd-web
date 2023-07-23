<div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Sender Id Information</div>
                    
                    <div class="panel-body">
                    
                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <?php //echo "<pre>"; print_r($clients); echo "</pre>"; ?>
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
                                 
                                    @foreach($clientsenderids as $assignedclient)
                                      @foreach($assignedclient->senderids as $assignedclient)
                                      <tr>
                                        <td>{{$assignedclient->reseller->name}}</td>
                                        <td class="text-center">
                                          @if(Auth::guard('root')->check())
                                          <a href="#" data-assign_user_senderid="{{$assignedclient->reseller_id}}" data-sms_sender_id="{{$assignedclient->sms_sender_id}}" class="btn btn-sm btn-icon btn-pure btn-default senderidresellerdtl"><i class="icon icon-trash"></i></a>
                                          @endif
                                        </td>
                                      </tr>
                                      @endforeach
                                    @endforeach
                                </tbody>
                              </table>
                            </div>
                            <div class="col-md-6">
                              <label for="activeclients">Select Reseller</label>
                              <select ui-jq="chosen"  id="activeclients" name="activeclients[]" multiple class="form-control w-md">
                              
                              <?php $clientname = []; $uniqueclient = [];?>    
                              @foreach($clients as $senderclient)
                                    
                                
                               
                                        
                                        <option value="{{$senderclient->id}}">{{$senderclient->name}}</option>
                                        
                                     
                                @endforeach 
                                
                              </select>
                            </div>
                          </div> 
                        </div>
                        
                        
                      
                    </div>
                  </div>
              </div>

<script src="{{ asset('smsapp/js/ui-load.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-jp.js') }}"></script>