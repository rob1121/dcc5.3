<?php namespace App\Observers;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     */
    public function created() {
        flash("User successfully added to database!.","success");
    }

    public function saved()
    {
        flash("User Database successfully updated!.","success");
    }
}