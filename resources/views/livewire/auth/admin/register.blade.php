<div>
    <section class="login d-flex flex-wrap">
      <div class="login-left bg-white">
        <div class="login-left-inner">
            <div class="login-quote">
              <h4>Welcome to {{config('constants.app_name')  }}</h4>
              <p>It is an e-learning platform where you can learn different type of skills that will be helpful to create a better future for you.it is an e-learning platform where you can learn.</p>
            </div>
            <div class="login-img-left">
              <img src="{{asset('images/login-left.png')}}" alt="login img">
            </div>
        </div>
      </div>
      <div class="login-right bg-light-orange">
        <div class="login-form">
          <div class="form-head">
            <h3>Nice to see you again!</h3>
          </div>
          <form class="form">            
            <div class="form-outer">
                <div class="form-group col-50">
                  <div class="login-icon"><img src="{{asset('images/icons/user.svg')}}" alt="User"></div>
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" placeholder="First Name" />
                </div>
                <div class="form-group col-50">
                  <div class="login-icon"><img src="{{asset('images/icons/user.svg')}}" alt="User"></div>
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" placeholder="Last Name" />
                </div>
                <div class="form-group">
                  <div class="login-icon"><img src="{{asset('images/icons/email.svg')}}" alt="User"></div>
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" placeholder="Enter Your Email" />
                </div>
                <div class="form-group">
                  <div class="login-icon"><img src="{{asset('images/icons/phone.svg')}}" alt="User"></div>
                  <label class="form-label">Phone Number</label>
                  <input type="number" class="form-control" placeholder="Phone Number" />
                </div>
                <div class="form-group col-50">
                  <div class="login-icon"><img src="{{asset('images/icons/date.svg')}}" alt="User"></div>
                  <label class="form-label">DOB</label>
                  <input type="date" class="form-control" placeholder="Phone Number" />
                </div>
                <div class="form-group col-50">
                  <label class="form-label">Gender</label>
                  <select class="form-control">
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                  </select>
                </div>
                <div class="form-group no-icon col-50">
                  <label class="form-label">Referral ID</label>
                  <input type="text" class="form-control" placeholder="XXXXXXX" />
                </div>
                <div class="form-group no-icon col-50">
                  <label class="form-label">Referral Name</label>
                  <input type="text" class="form-control" placeholder="Referral Name" />
                </div>

                <div class="form-group">
                  <div class="login-icon"><img src="{{asset('images/icons/map.svg')}}" alt="User"></div>
                  <label class="form-label">Address</label>
                  <input type="text" class="form-control" placeholder="Address here" />
                </div>
              </div>
              <div class="submit-btn">
                <button type="submit" class="btn ">SignUp</button>
              </div>
              <div class="have-account">
                <p>Already Have an account? <a href="{{ route('auth.login') }}">Login Now!</a></p>
              </div>
            </form>
        </div>
      </div>
    </section>
</div>
