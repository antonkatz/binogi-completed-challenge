namespace App\RemoteServices;

/**
* The format in which TokenContainer expects an access token to be in.
* This is Spotify's format, and should be generalized if other services are used in the future.
*/
interface RawToken {
    /** String value of the access token */
    public $access_token
    /** Time in seconds until expiry at the time of token creation */
    public $expires_in
}
