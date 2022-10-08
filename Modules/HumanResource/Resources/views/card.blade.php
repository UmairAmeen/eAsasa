<div class="col-lg-3" data-id='searchthis' style="flex: 0 0 25%; max-width:25%; margin-bottom: 20px;">

    <div class="employee-card" >

        <div class="head-body">
    
            <div class="card-head" >
                 <h3> 
                    <span data-id='searchable'>Employee # {{$employ->id}}</span>
                    <span data-id='searchable' class="delete-icon" onclick="deletethis(this,{{$employ->id}})">
                        <i class="fa fa-trash"></i>
                    </span>
                    <br>
                    <span data-id='searchable'>{{str_limit($employ->position, $limit = 15, $end = '..')}}</span><br>
                 </h3>  


            </div>
            <div class="card-body">
                <div class="employee-picture" >
                    <img width="100" height="100" src={{profilepic($employ->picture)}} onerror="this.onerror=null;this.src='{{ asset('assets/images/user.png') }}';">              
                </div>
            </div> 

        </div>


            <div class=" employee-name ">
                <a data-id='searchable' class=" btn"
                 href="{{url('/hr/show')."/".$employ->id}}" 
                > {{$employ->name}}</a>      

            </div>



            <div class="card-footer"> 
                <a class="nav-link btn"  
                    style="border:1px solid #ebedf2;"
                    href="{{url('/hr/show')."/".$employ->id}}">Profile
                </a> 
                <a class="nav-link btn" 
                    style="border:1px solid #ebedf2;"
                    href="{{ url('/hr/salary')."/".$employ->id}}">Salary
                </a>

            </div>

    </div>
</div>