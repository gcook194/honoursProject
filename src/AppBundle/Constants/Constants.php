<?php

namespace AppBundle\Constants;


final class Constants
{
    //User types
    const USER_TYPE_ADMIN = "admin";
    const USER_TYPE_LEAGUE_ADMIN = "leagueAdmin";
    const USER_TYPE_LEAGUE_USER = "user";

    //Roles
    const ROLE_SUPER_ADMIN = "ROLE_SUPER_ADMIN";

    //League Meta Defaults
    const DEFAULT_WIN_POINTS = 3;
    const DEFAULT_DRAW_POINTS = 1;
    const DEFAULT_SORT_COL_1 = "goalDifference";
    const DEFAULT_SORT_COL_2 = "goalsScored";
    const DEFAULT_SORT_COL_3 = "name";

    //team name default
    const DEFAULT_TEAM_NAME = "NewTeam";

    //league table defaults
    const DEFAULT_FORM_WIN = "W";
    const DEFAULT_FORM_LOSS = "L";
    const DEFAULT_FORM_DRAW = "D";

    //URL consts
    const URL_REFERRER_LEAGUE = "league";
}