<?php namespace Modules\Backend\Libraries;

use CodeIgniter\Events\Events;
use CodeIgniter\I18n\Time;
use Config\App;
use ci4mongodblibrary\Models\CommonModel;
use Config\Services;
use Modules\Backend\Config\Auth;
use Modules\Backend\Exceptions\AuthException;
use Modules\Backend\Models\UserModel;
use MongoDB\BSON\ObjectId;
<<<<<<< HEAD
=======
use MongoDB\BSON\UTCDateTime;
>>>>>>> dev

class AuthLibrary
{
    protected $userModel;
    protected $config;
    public $error;
    protected $user;
    protected $commonModel;


    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->config = new Auth();
        $this->commonModel = new CommonModel();
        $this->user = null;
        $this->config->userTable = 'users';
        $this->ipAddress = Services::request()->getIPAddress();
<<<<<<< HEAD
=======
        $this->now = Time::createFromFormat('Y-m-d H:i:s',new Time('now'),'Europe/Istanbul');
>>>>>>> dev

    }

    public function login(object $user = null, bool $remember = false): bool
    {
        if (empty($user)) {
            $this->user = null;
            return false;
        }
        $this->user = $user;
        $groupSefLink = $this->commonModel->getOne('auth_groups', ['_id' => new ObjectId($this->user->group_id)], ['seflink' => true]);

<<<<<<< HEAD
=======

        $where_or = ['username' => $user->email , 'ip_address' => $this->ipAddress];
        $set_data = ['islocked' => false] ;
        $this->userModel->updateManyOr('locked',['islocked' => true],$set_data,[],$where_or);

>>>>>>> dev
        session()->set('redirect_url', $groupSefLink->seflink);

        $this->userModel->recordLoginAttempt($this->user->email, true);
        // Regenerate the session ID to help protect against session fixation
        if (ENVIRONMENT !== 'testing') {
            session()->regenerate();
        }

        session()->set($this->config->logged_in, (string)$this->user->_id);

        Services::response()->noCache();

        if ($remember && $this->config->allowRemembering) {
            $this->rememberUser($this->user->_id);
        }

        if (mt_rand(1, 100) < 20) {
            $this->userModel->purgeOldRememberTokens();
        }

        // trigger login event, in case anyone cares
        Events::trigger('backend/login', $user);

        return true;
    }

    public function rememberUser(string $userID)
    {
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(20));
        $expires = date('Y-m-d H:i:s', time() + $this->config->rememberLength);

        $token = $selector . ':' . $validator;

        // Store it in the database
        $this->userModel->rememberUser($userID, $selector, hash('sha256', $validator), $expires);

        // Save it to the user's browser in a cookie.
        $appConfig = new App();
        $response = Services::response();

        // Create the cookie
        $response->setCookie(
            'remember',                     // Cookie Name
            $token,                         // Value
            $this->config->rememberLength,  // # Seconds until it expires
            $appConfig->cookieDomain,
            $appConfig->cookiePath,
            $appConfig->cookiePrefix,
            true,                          // Only send over HTTPS?
            true                            // Hide from Javascript?
        );
    }

    public function isLoggedIn(): bool
    {
        if ($userID = session($this->config->logged_in)) {
            // Store our current user object
<<<<<<< HEAD
            $this->user = $this->userModel->findOne(['_id' => new ObjectId($userID)]);
            $groupSefLink = $this->userModel->getGroupInfos(['_id' => new ObjectId($this->user->group_id)], ['seflink']);
=======
            $this->user = $this->commonModel->getOne($this->config->userTable,['_id' => new ObjectId($userID)]);
            $groupSefLink = $this->commonModel->getOne('auth_groups',['_id' => new ObjectId($this->user->group_id)], ['seflink']);
>>>>>>> dev
            session()->set('redirect_url', $groupSefLink->seflink);
            return true;
        }

        return false;
    }

    public function logout()
    {
        helper('cookie');
        $oid = new ObjectId(session($this->config->logged_in));
        if ($userID = $oid) {
<<<<<<< HEAD
            $this->user = $this->userModel->findOne(['_id' => (object)$userID]);
=======
            $this->user = $this->commonModel->getOne($this->config->userTable,['_id' => (object)$userID]);
>>>>>>> dev
        }

        $user = $this->user;

        // Destroy the session data - but ensure a session is still
        // available for flash messages, etc.
        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                $_SESSION[$key] = NULL;
                unset($_SESSION[$key]);
            }
        }

        // Regenerate the session ID for a touch of added safety.
        session()->regenerate(true);

        // Take care of any remember me functionality
<<<<<<< HEAD
        $this->userModel->purgeRememberTokens($user->_id);
=======
        $this->commonModel->deleteOne('auth_tokens',['user_id'=>$user->_id]);
>>>>>>> dev

        // trigger logout event
        Events::trigger('logout', $user);
    }

    public function has_perm(string $module, string $method): bool
    {
        if ($method == 'error_403')
            return true;

        $userInfo = $this->commonModel->getOne($this->config->userTable, ['_id' => new ObjectId(session()->get($this->config->logged_in))], ['projection' => ['group_id' => true, 'auth_users_permissions' => true]]);

        $module = str_replace('\\', '-', $module);
        $where=['className' => $module, 'methodName' => $method];

        if(empty($method))
            $where=['_id'=>new ObjectId($module)];

        $classID = $this->commonModel->getOne('auth_permissions_pages', $where, ['projection' => ['typeOfPermissions' => true]]);
        $perms = $this->commonModel->getOne('auth_groups', ['_id' => $userInfo->group_id], ['projection' => ['auth_groups_permissions' => true]]);
        $permissions = (array)$perms->auth_groups_permissions;

        $allPerms = [];
        if (!empty($userInfo->auth_users_permissions)) {//kullanıcıya atanmış izinler
            $userPerms = (array)$userInfo->auth_users_permissions;
            $allPerms = array_merge($permissions, $userPerms);
        } else
            $allPerms = $permissions;

        if (!empty($classID)) {

            $perms = [];
            foreach ($allPerms as $allPerm) {
                if ((string)$allPerm->page_id == (string)$classID->_id) {
                    $perms[] = $allPerm;
                }
            }

            $allPerms = [];
            $c = 0;

            foreach ($perms as $key => $perm) {
                if ($key > 0)
                    $c = $key - 1;
                if ($perm->create_r === true && $perms[$c]->create_r === $perm->create_r)
                    $allPerms[0]['create_r'] = true;
                if ($perm->read_r === true && $perms[$c]->read_r === $perm->read_r)
                    $allPerms[0]['read_r'] = true;
                if ($perm->update_r === true && $perms[$c]->update_r === $perm->update_r)
                    $allPerms[0]['update_r'] = true;
                if ($perm->delete_r === true && $perms[$c]->delete_r === $perm->delete_r)
                    $allPerms[0]['delete_r'] = true;
            }

            if (empty($allPerms))
                return false;

            $typeOfPerms = (array)$classID->typeOfPermissions;
            $intersect = array_intersect($typeOfPerms, $allPerms[0]);

            if (!empty($intersect))
                return true;
            else
                return false;
        } else
            return false;
    }

    public function attempt(array $credentials, bool $remember = null): bool
    {
<<<<<<< HEAD
        $this->user = $this->validate($credentials, true);
        $falseLogin = $this->commonModel->getOne('auth_logins',['ip_address' => $this->ipAddress],['sort'=> ['_id'=>-1]]);

        if ($falseLogin->isSuccess === false ){
            if(!isset($falseLogin->counter))
                $falseCounter = 0;
=======

        $this->user = $this->validate($credentials, true);
        $falseLogin = $this->commonModel->getOne('auth_logins',['ip_address' => $this->ipAddress],['sort'=> ['_id'=>-1]]);
        $settings = $this->commonModel->getOne('settings', [/* where */], [/* options */], ['loginBlockMin', 'loginBlockIsActive', 'lockedTry']);

        // Kalan deneme hakkı hesaplanıyor.
        if ($falseLogin && $falseLogin->isSuccess === false ){
            if ($falseLogin->counter &&  ((int)$falseLogin->counter + 1 )  >= (int)$settings->lockedTry ) $falseCounter = -1;
>>>>>>> dev
            else $falseCounter = $falseLogin->counter;
        } else $falseCounter = null;


        if (empty($this->user)) {
            // Always record a login attempt, whether success or not.
            $this->userModel->recordLoginAttempt($credentials['email'], false, (int)$falseCounter);
<<<<<<< HEAD

=======
>>>>>>> dev
            $this->user = null;
            return false;
        }

        if ($this->isBanned($this->user->_id)) {
            // Always record a login attempt, whether success or not.
            $this->userModel->recordLoginAttempt($credentials['email'], false, (int)$falseCounter);

            $this->error = lang('Auth.userIsBanned');

            $this->user = null;
            return false;
        }

        if (!$this->isActivated($this->user->_id)) {
            // Always record a login attempt, whether success or not.
            $this->userModel->recordLoginAttempt($credentials['email'], false, (int)$falseCounter);

            $param = http_build_query([
                'login' => urlencode($credentials['email'] ?? $credentials['username'])
            ]);

            $this->error = lang('Auth.notActivated') . ' ' . anchor(route_to('backend/resend-activate-account') . '?' . $param, lang('Auth.activationResend'));

            $this->user = null;
            return false;
        }

        return $this->login($this->user, $remember);
    }

    public function validate(array $credentials, bool $returnUser = false)
    {
        // Can't validate without a password.
        if (empty($credentials['password']) || count($credentials) < 2) {
            return false;
        }

        // Only allowed 1 additional credential other than password
        $password = $credentials['password'];
        unset($credentials['password']);

        if (count($credentials) > 1) {
            throw AuthException::forTooManyCredentials();
        }

        // Ensure that the fields are allowed validation fields
        if (!in_array(key($credentials), $this->config->validFields)) {
            throw AuthException::forInvalidFields(key($credentials));
        }

        // Can we find a user with those credentials?
<<<<<<< HEAD
        $user = $this->userModel->findOne($credentials);

        if (!$user) {
            $this->error = lang('Auth.badAttempt');
=======
        $user = $this->commonModel->getOne($this->config->userTable,$credentials);

        if (!$user) {
            $this->error = lang('Auth.badAttempt');
            //$this->error = sprintf(lang('Auth.badAttempt'), $this->remainingEntry());
>>>>>>> dev
            return false;
        }

        // Now, try matching the passwords.
        $result = password_verify(base64_encode(
            hash('sha384', $password, true)
        ), $user->password_hash);

        if (!$result) {
<<<<<<< HEAD
            $this->error = lang('Auth.invalidPassword');
=======
            $this->error = sprintf(lang('Auth.invalidPassword'), '<br><b>Kalan deneme hakkınız ' . $this->remainingEntryCalculation() . ' tanedir.<b></b>');
            //$this->error = lang('Auth.invalidPassword');
>>>>>>> dev
            return false;
        }

        // Check to see if the password needs to be rehashed.
        // This would be due to the hash algorithm or hash
        // cost changing since the last time that a user
        // logged in.
        if (password_needs_rehash($user->password_hash, $this->config->hashAlgorithm)) {
            $user->password_hash = $password;
            $this->userModel->passwordRehash(['_id' => $user->_id], $user);
        }

        return $returnUser ? $user : true;
    }

    public function isBanned($pk): bool
    {
<<<<<<< HEAD
        $userStatus = $this->userModel->findOne(['_id' => $pk], ['status']);
=======
        $userStatus = $this->commonModel->getOne($this->config->userTable,['_id' => $pk], ['status']);
>>>>>>> dev
        return isset($userStatus->status) && $userStatus->status === 'banned';
    }

    public function isActivated($pk): bool
    {
<<<<<<< HEAD
        $userStatus = $this->userModel->findOne(['_id' => $pk], ['status']);
=======
        $userStatus = $this->commonModel->getOne($this->config->userTable,['_id' => $pk], ['status']);
>>>>>>> dev
        return isset($userStatus->status) && $userStatus->status == 'active';
    }

    public function error()
    {
        return $this->error;
    }

    public function check(): bool
    {
        if ($this->isLoggedIn()) {
            return true;
        }

        // Check the remember me functionality.
        helper('cookie');
        $remember = get_cookie('remember');

        if (empty($remember)) {
            return false;
        }

        [$selector, $validator] = explode(':', $remember);
        $validator = hash('sha256', $validator);

<<<<<<< HEAD
        $token = $this->userModel->getRememberToken($selector);
=======
        $token = $this->commonModel->getOne('auth_tokens',['selector' => $selector]);
>>>>>>> dev

        if (empty($token)) {
            return false;
        }

        if (!hash_equals($token->hashedValidator, $validator)) {
            return false;
        }

        // Yay! We were remembered!
        $user = $this->commonModel->getOne($this->config->userTable, ['_id' => new ObjectId($token->user_id)]);

        if (empty($user)) {
            return false;
        }

        $this->login($user);

        // We only want our remember me tokens to be valid
        // for a single use.
        $this->refreshRemember($user->_id, $selector);

        return true;
    }

    public function refreshRemember(string $userID, string $selector)
    {
<<<<<<< HEAD
        $existing = $this->userModel->getRememberToken($selector);
=======
        $existing = $this->commonModel->getOne('auth_tokens',['selector'=>$selector]);
>>>>>>> dev

        // No matching record? Shouldn't happen, but remember the user now.
        if (empty($existing)) {
            return $this->rememberUser($userID);
        }

        // Update the validator in the database and the session
        $validator = bin2hex(random_bytes(20));

        $this->userModel->updateRememberValidator($selector, $validator);

        // Save it to the user's browser in a cookie.
        helper('cookie');

        $appConfig = new App();

        // Create the cookie
        set_cookie(
            $this->config->rememberCookie,               // Cookie Name
            $selector . ':' . $validator, // Value
            $this->config->rememberLength,  // # Seconds until it expires
            $appConfig->cookieDomain,
            $appConfig->cookiePath,
            $appConfig->cookiePrefix,
            false,                  // Only send over HTTPS?
            true                  // Hide from Javascript?
        );
    }

    public function setPassword(string $password)
    {
        if ((defined('PASSWORD_ARGON2I') && $this->config->hashAlgorithm == PASSWORD_ARGON2I) || (defined('PASSWORD_ARGON2ID') && $this->config->hashAlgorithm == PASSWORD_ARGON2ID)) {
            $hashOptions = [
                'memory_cost' => $this->config->hashMemoryCost,
                'time_cost' => $this->config->hashTimeCost,
                'threads' => $this->config->hashThreads
            ];
        } else {
            $hashOptions = [
                'cost' => $this->config->hashCost
            ];
        }

        $passwordHash = password_hash(
            base64_encode(
                hash('sha384', $password, true)
            ),
            $this->config->hashAlgorithm,
            $hashOptions
        );

        return $passwordHash;
    }

    function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function generateActivateHash()
    {
        return bin2hex(random_bytes(16));
    }

<<<<<<< HEAD
    public function isBloackedIp(): bool
    {
        $settings = $this->commonModel->getOne('settings', [/* where */], [/* options */], ['loginBlockMin', 'loginBlockIsActive', 'loginBlockAttemptsCounter']);
        $loginAttempts = $this->commonModel->getOne('auth_logins', ['ip_address' => $this->ipAddress, 'isSuccess' => false], ['sort' => ['_id' => -1]]);

        if ($loginAttempts !== null && $settings->loginBlockIsActive && $settings->loginBlockAttemptsCounter <= $loginAttempts->counter) {
            $now = new Time('now');
            $tryDate = new Time($loginAttempts->trydate);
            $blockFinisTime = $tryDate->addMinutes($settings->loginBlockMin);

            if ($now->isBefore($blockFinisTime)) return false; else return true;
        } else return true;
=======
    /* if return is true user blocked else login active */
    public function isBlockedAttempt ($username) : bool {
        $settings = $this->commonModel->getOne('settings', [/* where */], [/* options */]);
        if($settings->lockedIsActive){

            $whitlist = $this->commonModel->getOne('login_rules', ['type' => 'whitelist']);
            if ($whitlist){
                foreach ($whitlist->username as $locked_username)
                    if ($locked_username === $username) return false;

                foreach ($whitlist->line as $line)
                    if ($line === $this->ipAddress) return false;

                foreach ($whitlist->range as $range)
                    if ($this->ipRangeControl($range,$this->ipAddress))  return false;
            }

            $blacklist = $this->commonModel->getOne('login_rules', ['type' => 'blacklist']);
            if ($blacklist) {
                foreach ($blacklist->username as $locked_username)
                    if ($locked_username === $username) return true;

                foreach ($blacklist->username as $line)
                    if ($line === $this->ipAddress) return true;

                foreach ($blacklist->range as $range)
                    if ($this->ipRangeControl($range,$this->ipAddress))  return true;
            }

            $where = ['islocked' => true];
            $where_or = ['username' => $username, 'ip_address' => $this->ipAddress];
            $countLocked = $this->userModel->getOneOr('locked', $where, ['sort' => ['_id' => -1]], ['_id','counter','expiry_date'], $where_or);

            if (!$countLocked) $countLockedValue = 0;
            else $countLockedValue = $countLocked->counter;

            if ((int)$settings->lockedRecord <= $countLockedValue){
                $this->commonModel->updateOne('locked', ['_id' => $countLocked->_id],['counter' => 0]);
                return false;
            }

            $where = ['islocked' => true,'expiry_date' => ['$gte' => $this->now->toDateTimeString()]];
            $where_or = ['username' => $username, 'ip_address' => $this->ipAddress];
            $lockedNow = $this->userModel->countOr('locked',$where,[/*option*/],$where_or);
            if ($lockedNow !== 0){
                $this->error = "Hesabınız saat : <b>".Time::createFromFormat('Y-m-d H:i:s', new Time($countLocked->expiry_date),'Europe/Istanbul')->toLocalizedString('d-MMMM hh:mm z')."</b> tariğine kadar bloklanmıştır.";
                return true;
            }

            $loginAttempts = $this->userModel->getOneOr('auth_logins', ['isSuccess' => false], ['sort' => ['_id' => -1]],['id','counter'],$where_or);

            if( $loginAttempts && isset($loginAttempts->counter) && ($loginAttempts->counter+1)  >= (int)$settings->lockedTry ){
                if (( $countLockedValue + 1 ) < ((int)$settings->lockedRecord))
                    $expiry_date = Time::createFromFormat('Y-m-d H:i:s', $this->now->addMinutes((int)$settings->lockedMin));
                else {
                    $countLockedValue = - 1 ;
                    $expiry_date = Time::createFromFormat('Y-m-d H:i:s',$this->now->addMinutes(1440)); // 24 hours ago
                }

                $this->commonModel->createOne('locked',[
                    'type' => null,
                    'ip_address' => $this->ipAddress,
                    'username' => $username,
                    'isLocked' => true,
                    'counter' => ($countLockedValue+1),
                    'locked_at' => $this->now->toDateTimeString(),
                    'expiry_date' => $expiry_date->toDateTimeString(),
                ]);

                return false;
            } else return false;
        } else return false;
    }

    public function ipRangeControl($range, $ipAddress) :bool {
        $parseRange = explode('-',$range);
        if( $this->ipFormatContol($ipAddress, $parseRange[0],$parseRange[1]) && // ipler aynı formattalar mı ?
            $this->ip2long_vX($ipAddress) >= $this->ip2long_vX($parseRange[0]) &&
            $this->ip2long_vX($ipAddress) <= $this->ip2long_vX($parseRange[1]))
            return true;

        else return false;
    }

    /* if all ip's same format is true else false */
    /** TODO range iplerdein sadece birinin formatına bakılması yeterli */
    public function ipFormatContol($ipAddress, $rangeStart, $rangeEnd) : bool
    {
        $ips = array ('ipAddress' => $ipAddress,'rangeStart' => $rangeStart,'rangeEnd' => $rangeEnd);
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) $ipsFormat [] = 'ip4' ;
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) $ipsFormat [] = 'ip6';
        }
        /* All array value are same value ? */
        if (count(array_unique($ipsFormat)) === 1 ) return true;
        else return false;
    }

    /* ip address type convert to integer. */
    public function ip2long_vX($ip) {
        $ip_n = inet_pton($ip);
        $bin = '';
        for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
        }
        if (function_exists('gmp_init')) {
            return (int)gmp_strval(gmp_init($bin, 2), 10);
        } elseif (function_exists('bcadd')) {
            $dec = '0';
            for ($i = 0; $i < strlen($bin); $i++) {
                $dec = bcmul($dec, '2', 0);
                $dec = bcadd($dec, $bin[$i], 0);
            }
            return (int)$dec;
        } else {
            trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
        }
    }

    public function remainingEntryCalculation() {
        $falseLogin = $this->commonModel->getOne('auth_logins',['ip_address' => $this->ipAddress],['sort'=> ['_id'=>-1]]);
        $settings = $this->commonModel->getOne('settings', [/* where */], [/* options */], ['loginBlockMin', 'loginBlockIsActive', 'lockedTry']);
        if ($falseLogin) return (int)$settings->lockedTry - (int)$falseLogin->counter - 1 ;
        else return (int)$settings->lockedTry - 1 ;
>>>>>>> dev
    }
}