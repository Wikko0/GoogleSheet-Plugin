<?php

namespace Wikko\Googlesheet\Controllers;

use Acelle\Model\MailList;
use Acelle\Model\Subscriber;
use Acelle\Model\SubscriberField;
use Sheets;
use Exception;
use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller as BaseController;
use Wikko\Googlesheet\Model\GoogleList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


class GoogleController extends BaseController
{
    public function index(Request $request)
    {

        return view('googlesheet::list');
    }

    public function create(Request $request)
    {
        $customer = $request->user()->customer;

        $list = $customer->mailLists()
            ->get();


        return view('googlesheet::create', [
            'list' => $list,
        ]);
    }

    public function store(Request $request)
    {
        /* Validation */
        $customer = $request->user()->customer;
        $validatedData = $request->validate([
            'connection_name' => 'required|string|max:255',
            'connection_type' => 'required|string|max:255',
            'list_uid' => 'required|string|max:255',
            'sheet_name' => 'required|string|max:255',
            'sheet_id' => 'required|string|max:255',
        ]);

        /* Full sync */
        if ($validatedData['connection_type'] == 'Full sync'){

            try{
                Sheets::spreadsheet($validatedData['sheet_id'])->addSheet($validatedData['sheet_name']);
                $addSheet = true;
            } catch(Exception $e) {
                $addSheet = null;
            }

                $list_name = $customer->mailLists()
                    ->where('uid', $validatedData['list_uid'])
                    ->first();

                /* Get Subscribers */
                $subscribers = Subscriber::where('mail_list_id', $list_name->id)->get();

                try {
                    $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);

                    /* Add Subscribers */
                    foreach ($subscribers as $subscriber) {
                        $fields = [];
                        $subscriber_fields = SubscriberField::where('subscriber_id', $subscriber->id)->get();
                        foreach ($subscriber_fields as $field) {
                            $fields[] = $field->value;
                        }
                        $sheet->append([$fields]);
                    }
                } catch (\Google_Service_Exception $e) {
                    return redirect()->back()->withErrors('Access denied. You must allow editing from Google Spreadsheet.');
                }

                $list = MailList::findByUid($validatedData['list_uid']);
                $customer = $request->user()->customer;
                if (!$customer->user->can('addMoreSubscribers', [$list, $more = 1])) {
                    return $this->noMoreItem();
                }

                // Validate & and create subscriber
                try{
                    $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);
                    $data = $sheet->get();
                } catch(\Google_Service_Exception $e) {
                    return redirect()->back()->withErrors('Wrong Google Sheet name or Spreadsheet ID');
                }


                $merge_data = [];

                foreach ($data as $row) {
                    $email_column = $row[0];
                    $first_name_column = $row[1];
                    $last_name_column = $row[2];

                    $merge_data[] = [
                        'EMAIL' => $email_column,
                        'FIRST_NAME' => $first_name_column,
                        'LAST_NAME' => $last_name_column
                    ];
                }

                foreach ($merge_data as $merge) {
                    $subscriber = Subscriber::where('email', $merge['EMAIL'])->first();
                    if ($subscriber === null) {
                        $request->merge($merge);
                        list($validator, $subscriber) = $list->subscribe($request, Subscriber::SUBSCRIPTION_TYPE_ADDED);

                        if (is_null($subscriber)) {
                            return back()->withInput()->withErrors($validator);
                        }
                    }
                }



        }

        /* Google Sheet Connection */
        if ($validatedData['connection_type'] == 'App to Sheets'){
            try{
                Sheets::spreadsheet($validatedData['sheet_id'])->addSheet($validatedData['sheet_name']);
            } catch(Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Spreadsheet ID');
            }

        }

        $list_name = $customer->mailLists()
            ->where('uid', $validatedData['list_uid'])
            ->first();

        /* App to Sheet */
        if ($validatedData['connection_type'] == 'App to Sheets'){

            /* Get Subscribers */
            $subscribers = Subscriber::where('mail_list_id', $list_name->id)->get();

            try {
                $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);

                /* Add Subscribers */
                foreach ($subscribers as $subscriber){
                    $fields = [];
                    $subscriber_fields = SubscriberField::where('subscriber_id', $subscriber->id)->get();
                    foreach ($subscriber_fields as $field){
                        $fields[] = $field->value;
                    }
                    $sheet->append([$fields]);
                }
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Access denied. You must allow editing from Google Spreadsheet.');
            }
        }

        /* Sheet to App */
        if ($validatedData['connection_type'] == 'Sheets to App') {

            $list = MailList::findByUid($validatedData['list_uid']);
            $customer = $request->user()->customer;
            if (!$customer->user->can('addMoreSubscribers', [$list, $more = 1])) {
                return $this->noMoreItem();
            }

            // Validate & and create subscriber
            try{
                $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);
                $data = $sheet->get();
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Sheet name or Spreadsheet ID');
            }


            $merge_data = [];

            foreach ($data as $row) {
                $email_column = $row[0];
                $first_name_column = $row[1];
                $last_name_column = $row[2];

                $merge_data[] = [
                    'EMAIL' => $email_column,
                    'FIRST_NAME' => $first_name_column,
                    'LAST_NAME' => $last_name_column
                ];
            }

            foreach ($merge_data as $merge) {
                $subscriber = Subscriber::where('email', $merge['EMAIL'])->first();
                if ($subscriber === null) {
                    $request->merge($merge);
                    list($validator, $subscriber) = $list->subscribe($request, Subscriber::SUBSCRIPTION_TYPE_ADDED);

                    if (is_null($subscriber)) {
                        return back()->withInput()->withErrors($validator);
                    }
                }
            }
        }

        /* Store in DB */
        $connection = new GoogleList([
            'uid' => $validatedData['list_uid'],
            'connection_name' => $validatedData['connection_name'],
            'connection_type' => $validatedData['connection_type'],
            'sheet_name' => $validatedData['sheet_name'],
            'google_sheet_id' => $validatedData['sheet_id'],
            'list_name' => $list_name->name,
        ]);

        $connection->last_sync = Carbon::now();
        $connection->save();

        return redirect()->action('\Wikko\Googlesheet\Controllers\GoogleController@index');

    }

    public function edit(Request $request, $id)
    {
        $customer = $request->user()->customer;
        $googleList = GoogleList::where('id',$id)->first();
        $list = $customer->mailLists()
            ->get();


        return view('googlesheet::edit', [
            'list' => $list, 'googleList' => $googleList
        ]);
    }

    public function update(Request $request,$id)
    {
        /* Get old Data*/
        $googleList = GoogleList::where('id',$id)->first();
        /* Validation */
        $customer = $request->user()->customer;
        $validatedData = $request->validate([
            'connection_name' => 'required|string|max:255',
            'connection_type' => 'required|string|max:255',
            'list_uid' => 'required|string|max:255',
            'sheet_name' => 'required|string|max:255',
            'sheet_id' => 'required|string|max:255',
        ]);

        /* Google Sheet Connection */
        if ($validatedData['connection_type'] == 'App to Sheets' && $validatedData['sheet_name'] != $googleList->sheet_name){
            try{
                Sheets::spreadsheet($validatedData['sheet_id'])->addSheet($validatedData['sheet_name']);
            } catch(Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Spreadsheet ID');
            }

        }

        $list_name = $customer->mailLists()
            ->where('uid', $validatedData['list_uid'])
            ->first();

        /* App to Sheet */
        if ($validatedData['connection_type'] == 'App to Sheets'){

            /* Get Subscribers */
            $subscribers = Subscriber::where('mail_list_id', $list_name->id)->get();

            try {
                $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);

                /* Add Subscribers */
                foreach ($subscribers as $subscriber){
                    $fields = [];
                    $subscriber_fields = SubscriberField::where('subscriber_id', $subscriber->id)->get();
                    foreach ($subscriber_fields as $field){
                        $fields[] = $field->value;
                    }
                    $sheet->append([$fields]);
                }
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Access denied. You must allow editing from Google Spreadsheet.');
            }
        }

        /* Sheet to App */
        if ($validatedData['connection_type'] == 'Sheets to App' && $validatedData['sheet_name'] != $googleList->sheet_name) {

            $list = MailList::findByUid($validatedData['list_uid']);
            $customer = $request->user()->customer;
            if (!$customer->user->can('addMoreSubscribers', [$list, $more = 1])) {
                return $this->noMoreItem();
            }

            // Validate & and create subscriber
            try{
                $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);
                $data = $sheet->get();
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Sheet name or Spreadsheet ID');
            }


            $merge_data = [];

            foreach ($data as $row) {
                $email_column = $row[0];
                $first_name_column = $row[1];
                $last_name_column = $row[2];

                $merge_data[] = [
                    'EMAIL' => $email_column,
                    'FIRST_NAME' => $first_name_column,
                    'LAST_NAME' => $last_name_column
                ];
            }

            foreach ($merge_data as $merge) {
                $subscriber = Subscriber::where('email', $merge['EMAIL'])->first();
                if ($subscriber === null) {
                    $request->merge($merge);
                    list($validator, $subscriber) = $list->subscribe($request, Subscriber::SUBSCRIPTION_TYPE_ADDED);

                    if (is_null($subscriber)) {
                        return back()->withInput()->withErrors($validator);
                    }
                }
            }
        }

        if ($validatedData['connection_type'] == 'Full Sync' && $validatedData['sheet_name'] != $googleList->sheet_name) {

            try{
                Sheets::spreadsheet($validatedData['sheet_id'])->addSheet($validatedData['sheet_name']);
            } catch(Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Spreadsheet ID');
            }

        }

        $list_name = $customer->mailLists()
            ->where('uid', $validatedData['list_uid'])
            ->first();

        /* App to Sheet */
        if ($validatedData['connection_type'] == 'App to Sheets'){

            /* Get Subscribers */
            $subscribers = Subscriber::where('mail_list_id', $list_name->id)->get();

            try {
                $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);

                /* Add Subscribers */
                foreach ($subscribers as $subscriber){
                    $fields = [];
                    $subscriber_fields = SubscriberField::where('subscriber_id', $subscriber->id)->get();
                    foreach ($subscriber_fields as $field){
                        $fields[] = $field->value;
                    }
                    $sheet->append([$fields]);
                }
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Access denied. You must allow editing from Google Spreadsheet.');
            }
            $list = MailList::findByUid($validatedData['list_uid']);
            $customer = $request->user()->customer;
            if (!$customer->user->can('addMoreSubscribers', [$list, $more = 1])) {
                return $this->noMoreItem();
            }

            // Validate & and create subscriber
            try{
                $sheet = Sheets::spreadsheet($validatedData['sheet_id'])->sheet($validatedData['sheet_name']);
                $data = $sheet->get();
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Sheet name or Spreadsheet ID');
            }


            $merge_data = [];

            foreach ($data as $row) {
                $email_column = $row[0];
                $first_name_column = $row[1];
                $last_name_column = $row[2];

                $merge_data[] = [
                    'EMAIL' => $email_column,
                    'FIRST_NAME' => $first_name_column,
                    'LAST_NAME' => $last_name_column
                ];
            }

            foreach ($merge_data as $merge) {
                $subscriber = Subscriber::where('email', $merge['EMAIL'])->first();
                if ($subscriber === null) {
                    $request->merge($merge);
                    list($validator, $subscriber) = $list->subscribe($request, Subscriber::SUBSCRIPTION_TYPE_ADDED);

                    if (is_null($subscriber)) {
                        return back()->withInput()->withErrors($validator);
                    }
                }
            }
        }

        /* Store in DB */
        $update = GoogleList::where('id', $id)->first();
        $update->uid = $validatedData['list_uid'];
        $update->connection_name = $validatedData['connection_name'];
        $update->connection_type = $validatedData['connection_type'];
        $update->sheet_name = $validatedData['sheet_name'];
        $update->google_sheet_id = $validatedData['sheet_id'];
        $update->list_name = $list_name->name;
        $update->last_sync = Carbon::now();
        $update->save();

        return redirect()->action('\Wikko\Googlesheet\Controllers\GoogleController@index');

    }

    public function synchronize(Request $request)
    {

        /* Get connection */
        $list = GoogleList::where('id', $request->id)->first();
        $customer = $request->user()->customer;

        /* App to Sheet */
        if ($list->connection_type == 'App to Sheets'){

            /* List name */
            $list_name = $customer->mailLists()
                ->where('uid', $list->uid)
                ->first();


            /* Get Subscribers */
            $subscribers = Subscriber::where('mail_list_id', $list_name->id)->get();


            try {
                $sheet = Sheets::spreadsheet($list->google_sheet_id)->sheet($list->sheet_name);

                /* Add Subscribers */
                foreach ($subscribers as $subscriber){
                    $fields = [];
                    $subscriber_fields = SubscriberField::where('subscriber_id', $subscriber->id)->get();
                    foreach ($subscriber_fields as $field){
                        foreach ($sheet->get() as $row){
                            foreach ($row as $cell) {
                                if ($cell == $field->value) {

                                    break 3;
                                }
                            }
                        }
                        $fields[] = $field->value;
                    }
                    $sheet->append([$fields]);
                }


            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Access denied. You must allow editing from Google Spreadsheet.');
            }

        }

        /* Full sync */
        if ($list->connection_type == 'Full sync'){

            /* List name */
            $list_name = $customer->mailLists()
                ->where('uid', $list->uid)
                ->first();


            /* Get Subscribers */
            $subscribers = Subscriber::where('mail_list_id', $list_name->id)->get();


            try {
                $sheet = Sheets::spreadsheet($list->google_sheet_id)->sheet($list->sheet_name);

                /* Add Subscribers */
                foreach ($subscribers as $subscriber){
                    $fields = [];
                    $subscriber_fields = SubscriberField::where('subscriber_id', $subscriber->id)->get();
                    foreach ($subscriber_fields as $field){
                        foreach ($sheet->get() as $row){
                            foreach ($row as $cell) {
                                if ($cell == $field->value) {

                                    break 3;
                                }
                            }
                        }
                        $fields[] = $field->value;
                    }
                    $sheet->append([$fields]);
                }


            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Access denied. You must allow editing from Google Spreadsheet.');
            }

            $mailList = MailList::findByUid($list->uid);
            $customer = $request->user()->customer;
            if (!$customer->user->can('addMoreSubscribers', [$mailList, $more = 1])) {
                return $this->noMoreItem();
            }

            // Validate & and create subscriber
            try{
                $sheet = Sheets::spreadsheet($list->google_sheet_id)->sheet($list->sheet_name);
                $data = $sheet->get();
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Sheet name or Spreadsheet ID');
            }


            $merge_data = [];

            foreach ($data as $row) {
                $email_column = $row[0];
                $first_name_column = $row[1];
                $last_name_column = $row[2];

                $merge_data[] = [
                    'EMAIL' => $email_column,
                    'FIRST_NAME' => $first_name_column,
                    'LAST_NAME' => $last_name_column
                ];
            }

            foreach ($merge_data as $merge) {
                $subscriber = Subscriber::where('email', $merge['EMAIL'])->first();
                if ($subscriber === null) {
                    $request->merge($merge);
                    list($validator, $subscriber) = $mailList->subscribe($request, Subscriber::SUBSCRIPTION_TYPE_ADDED);

                    if (is_null($subscriber)) {
                        return back()->withInput()->withErrors($validator);
                    }
                }
            }

        }

        /* Sheet to App */
        if ($list->connection_type == 'Sheets to App') {

            $mailList = MailList::findByUid($list->uid);
            $customer = $request->user()->customer;
            if (!$customer->user->can('addMoreSubscribers', [$mailList, $more = 1])) {
                return $this->noMoreItem();
            }

            // Validate & and create subscriber
            try{
                $sheet = Sheets::spreadsheet($list->google_sheet_id)->sheet($list->sheet_name);
                $data = $sheet->get();
            } catch(\Google_Service_Exception $e) {
                return redirect()->back()->withErrors('Wrong Google Sheet name or Spreadsheet ID');
            }


            $merge_data = [];

            foreach ($data as $row) {
                $email_column = $row[0];
                $first_name_column = $row[1];
                $last_name_column = $row[2];

                $merge_data[] = [
                    'EMAIL' => $email_column,
                    'FIRST_NAME' => $first_name_column,
                    'LAST_NAME' => $last_name_column
                ];
            }

            foreach ($merge_data as $merge) {
                $subscriber = Subscriber::where('email', $merge['EMAIL'])->first();
                if ($subscriber === null) {
                    $request->merge($merge);
                    list($validator, $subscriber) = $mailList->subscribe($request, Subscriber::SUBSCRIPTION_TYPE_ADDED);

                    if (is_null($subscriber)) {
                        return back()->withInput()->withErrors($validator);
                    }
                }
            }
        }

        /* Store in DB */
        $list->last_sync = Carbon::now();

        $list->save();


        return redirect()->action('\Wikko\Googlesheet\Controllers\GoogleController@index');

    }


    public function listing(Request $request)
    {
        $customer = $request->user()->customer;
        $lists = $customer->mailLists()->get();
        $uids = [];

        foreach ($lists as $list) {
            $uids[] = $list->uid;
        }

        $googleLists = GoogleList::whereIn('uid', $uids)
            ->search($request->keyword)
            ->orderBy($request->sort_order, $request->sort_direction)
            ->paginate($request->per_page);


        return view('googlesheet::_list', [
            'lists' => $googleLists,
        ]);
    }

    public function delete(Request $request)
    {

        $lists = GoogleList::whereIn(
            'id',
            is_array($request->uids) ? $request->uids : explode(',', $request->uids)
        );

        foreach ($lists->get() as $item) {
                $item->delete();
        }

        // Redirect to my lists page
        echo trans('messages.lists.deleted');
    }

    public function deleteConfirm(Request $request)
    {
        $lists = GoogleList::whereIn(
            'id',
            is_array($request->uids) ? $request->uids : explode(',', $request->uids)
        );

        return view('googlesheet::delete_confirm', [
            'lists' => $lists,
        ]);
    }

    public function install()
    {
        Artisan::call('migrate');
        $sourceFilePath = resource_path('views/layouts/core/_menu_frontend.blade.php');
        $destinationFilePath = storage_path('app/plugins/wikko/googlesheet/_menu_frontend.blade.php');
        $contents = file_get_contents($destinationFilePath);
        File::put($sourceFilePath, $contents);

        $credentialsPath = storage_path('app/plugins/wikko/googlesheet/credentials.json');
        $credentialsDestinationFilePath = storage_path('credentials.json');
        File::move($credentialsPath, $credentialsDestinationFilePath);

        $output = shell_exec( 'cd '. base_path() .' && composer require revolution/laravel-google-sheets:*' );

        return 'Migrations executed. Composer output: <pre>' . $output . '</pre>';
    }

}
