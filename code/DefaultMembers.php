<?php

/**
 * Class DefaultMembers
 */
class DefaultMembers extends DataExtension
{

    /**
     * Create default members on /dev/build
     */
    public function requireDefaultRecords()
    {
        static::create_default_members();
        static::delete_default_members();
    }

    /**
     * Create default members
     *
     * @param bool $silent Pass true to hide `/dev/build` messages
     */
    public static function create_default_members($silent = false)
    {
        $adminGroup = Group::get()
            ->filter('Code', 'administrators')
            ->first();

        $admins = Config::inst()->get('DefaultMembers', 'admins');

        if ($admins && is_array($admins) && $adminGroup && $adminGroup->exists()) {
            foreach ($admins as $data) {

                if (!Email::is_valid_address($data['Email'])) {
                    continue;
                }

                $member = Member::get()
                    ->filter('Email', $data['Email'])
                    ->first();

                if (!$member || !$member->exists()) {
                    /**
                     * Create the user
                     */
                    $member = new Member();
                    $member->Locale = 'en_US';
                    $member->Email = $data['Email'];

                    if (isset($data['FirstName'])) {
                        $member->FirstName = $data['FirstName'];
                    }
                    if (isset($data['Surname'])) {
                        $member->Surname = $data['Surname'];
                    }

                    $member->write();
                    $adminGroup->Members()->add($member);

                    /**
                     * Automatically send password reset to user
                     */
                    $token = $member->generateAutologinTokenAndStoreHash();
                    $e = Member_ForgotPasswordEmail::create();
                    $e->populateTemplate($member);
                    $e->populateTemplate([
                        'PasswordResetLink' => Security::getPasswordResetLink($member, $token),
                    ]);
                    $e->setTo($member->Email);
                    $e->send();

                    /**
                     * Display the change
                     */
                    if (!$silent) {
                        DB::alteration_message("Added $member->Name ($member->Email) as a admin.", "created");
                    }
                    unset($member);
                }
            }
        }
    }

    /**
     * Remove default members
     *
     * @param bool $silent Pass true to hide `/dev/build` messages
     */
    public static function delete_default_members($silent = false)
    {
        $delete = Config::inst()->get('DefaultMembers', 'delete');

        if ($delete && is_array($delete)) {
            foreach ($delete as $email) {

                $members = Member::get()
                    ->filter('Email', $email);

                foreach ($members as $member) {
                    if ($member && $member->exists()) {
                        $member->delete();

                        /**
                         * Display the change
                         */
                        if (!$silent) {
                            DB::alteration_message("Deleted $member->Name ($member->Email).", "deleted");
                        }
                        unset($member);
                    }
                }
            }
        }
    }

}
