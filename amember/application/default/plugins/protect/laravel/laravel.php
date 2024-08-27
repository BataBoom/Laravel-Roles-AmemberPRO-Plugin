<?php
/**
 * Laravel + Spatie Roles Integration Plugin for Amember PRO
 * @table integration
 * @id laravel
 * @title Laravel
 * @hidden_link https://spatie.be/docs/laravel-permission/v5/introduction
 * @hidden_link https://www.amember.com/
 * @hidden_link https://github.com/BataBoom/
 * @description Integrate Laravel Roles with Amember Pro Subscriptions. Sync Subscriptions to Roles. 
 * Cookie Login in development.
 * This Plugin was built by BataBoom
 * @different_groups infinite
 * @type Artisans
 * @am_protect_api 6.3.31
 */
class Am_Protect_Laravel extends Am_Protect_Databased
{

    const PLUGIN_REVISION = '6.3.31';
    const PLUGIN_STATUS = self::STATUS_BETA;
    const PLUGIN_COMM = self::COMM_FREE;

    protected $guessTablePattern = "users";

    protected $guessFieldsPattern = [
        'id',
        'name',
        'email',
        'password',
        'created_at',
        'updated_at',
        //'amember_id' //not default laravel, but you could add it.
        ];

    protected $groupMode = Am_Protect_Databased::GROUP_MULTI;

    public $sqlDebug = false;

    public function afterAddConfigItems($form)
    {
        parent::afterAddConfigItems($form);

        $form->addText('protect.laravel.cookie')->setLabel('Laravel Cookie Name (in Development)');
    }

    public function createTable()
    {
        $table = new Am_Protect_Table($this, $this->getDb(), 'users', 'id');
        $table->setFieldsMapping([
            [Am_Protect_Table::FIELD_LOGIN, 'name'],
            [Am_Protect_Table::FIELD_EMAIL, 'email'],
            [Am_Protect_Table::FIELD_PASS, 'password'],
            [Am_Protect_Table::FIELD_ADDED_SQL, 'created_at'],
            [Am_Protect_Table::FIELD_ADDED_SQL, 'updated_at'],
            //[Am_Protect_Table::FIELD_ID, 'amember_id'],
        ]);
        
        $table->setGroupsTableConfig([
            Am_Protect_Table::GROUP_TABLE => 'model_has_roles',
            Am_Protect_Table::GROUP_GID => 'role_id',
            Am_Protect_Table::GROUP_UID => 'model_id',
            Am_Protect_Table::GROUP_ADDITIONAL_FIELDS => [
                'model_type' => 'App\Models\User'
            ],
        ]);
        
        return $table;
    }

    public function getAvailableUserGroupsSql()
    {
        return "SELECT
            id as id,
            name as title,
            NULL as is_banned, #must be customized
            NULL as is_admin # must be customized
            FROM ?_roles";
    }

    public function canAutoCreate()
    {
        return true;
    }

    public function canAutoCreateFromGroups()
    {
        return true;
    }


    function getSessionCookieName()
    {
        //return name of cookie that used for sessions
    }

    function getReadme()
    {
        return <<<CUT
        Configure Laravel Plugin Settings, then refer to Yellow Warning @ top for link and set that up.
        <br>
        Then proceed to Utilities -> Rebuild DB -> Rebuild Laravel DB. 
        <br>
        Rebuild Laravel DB as many time as needed (its safe =D).
        <br>
        Once set all subscriptions will be synced, and will stay in sync. You don't need to run Rebuild Laravel DB ever again. 
    CUT;
    }

    public function getPasswordFormat()
    {
        return SavedPassTable::PASSWORD_PASSWORD_HASH;
    }

    public function cryptPassword($pass, &$salt = null, User $user = null)
    {
        return password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}

class Am_Protect_Table_Laravel extends Am_Protect_Table
{

    public function insertFromAmember(User $user, SavedPass $pass, $groups)
    {
        $record = parent::insertFromAmember($user, $pass, $groups);

        return $record;
    }

    public function updateFromAmember(Am_Record $record, User $user, $groups)
    {
        $record = parent::updateFromAmember($record, $user, $groups);

        return $record;
    }
} 
