<?php

namespace App\Http\Controllers;

use App\Mail\ApprovedPR;
use App\Mail\ReportItemm;
use App\Mail\DeliveryNo;
use App\Mail\DenyPR;
use App\Mail\ForCanvass;
use App\Mail\GeneratedPO;
use App\Mail\GmailNotification;
use App\Mail\Report as MailReport;
use App\Mail\Requestor;
use App\Mail\Requestor_PO;
use App\Mail\SelectSupplier;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Building;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Staff;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestHistory;
use App\Models\PurchaseRequestItem;
use App\Models\Canvass;
use App\Models\CanvassItem;
use App\Models\PurchaseOrder;
use App\Models\Delivery;
use App\Models\DeniedRequest;
use App\Models\Forwarded;
use App\Models\DelFwd;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\SelectedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Exception;

class ApproverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:Approver']);
    }
    public function index()
    {
        $id = Auth::user()->id;
        // dd($id);
        $my_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->where('user_id', '=', $id)
            ->whereNOTIn('purchase_request_histories.action', ['Approved', 'Request Denied', 'Request on Hold'])
            ->count();
        $purchaseorder = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->where('user_id', '=', $id)->count();
        $toreceived = Delivery::join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                // 'purchase_requests.user_id',
            )
            ->where('purchase_requests.user_id', '=', $id)
            ->whereIn('del_fwds.status',  ['With Delivery No.', 'For Approval', 'Approved'])
            // ->where('del_fwds.status', '=', 'Approved')
            ->groupby(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                // 'purchase_requests.user_id',
            )
            ->get()
            // ->unique()
            ->count();
        $newpr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->where('purchase_request_histories.action', '=', 'New Purchase Request')
            ->get()
            ->count();
        $verificationpr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verifying')
            ->get()
            ->count();
        $totransmit = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->where('del_fwds.status', '=', 'For Approval')
            ->get()
            ->unique()
            ->count();
        $prforapproval = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Checked')
            ->get()
            ->count();
        $poforapproval = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Verified')
            ->get()
            ->count();
        $poforapproval2 = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approving')
            ->get()
            ->count();
        $userr = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.zipcode',
            )
            ->where('staff.id', '=', $id)
            ->first();
        // dd($toreceived);
        return view('approver.dashboard', compact('userr', 'poforapproval2', 'poforapproval', 'prforapproval', 'totransmit', 'verificationpr', 'newpr', 'my_pr', 'purchaseorder', 'toreceived'));
    }
    public function profile()
    {
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select('roles.display_name', 'roles.id')
            ->where('users.id', '=', $id)
            ->first();
        $userr = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                'staff.id',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'staff.sex',
                'staff.barangay',
                'staff.municipality',
                'staff.city',
                'staff.province',
                'staff.zipcode',
                'users.position',
                'staff.contact_no',
                'departments.Dept_name',
                'departments.id'
            )
            ->where('staff.id', '=', $id)
            ->first();
        $department = Department::all();
        return view('manage_profile.profile', compact('department', 'user', 'userr'));
    }
    public function updateprofile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|max:255',
            'mname' => 'required|max:255',
            'lname' => 'required|max:255',
            'sex' => 'required|max:255',
            'contact_no' => 'required|digits:10',
            'barangay' => 'required|max:255',
            'city' => 'required|max:255',
            'municipality' => 'required|max:255',
            'province' => 'required|max:255',
            'zipcode' => 'required|max:255',
            // 'position' => 'required|max:255',
            // 'department_id' => 'required|max:255',
            'email' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $users = User::find($id);
            if (!empty([$request->fname, $request->email])) {
                $users->name = $request->input('fname');
                $users->email = $request->input('email');
            } else {
            }
            $staff = Staff::find($id);
            if ($staff === null) {
                $staff = new Staff;
                $staff->id = $request->input('profile_id');
            } else {
            }
            if (!empty([
                $request->fname,
                $request->mname,
                $request->lname,
                $request->sex,
                $request->contact_no,
                // $request->email,
                $request->barangay,
                $request->municipality,
                $request->city,
                $request->province,
                $request->zipcode,
                // $request->position,
                $request->department_id,
            ])) {
                $staff->fname = $request->input('fname');
                $staff->mname = $request->input('mname');
                $staff->lname = $request->input('lname');
                $staff->sex = $request->input('sex');
                // $staff->email = $request->input('email');
                $staff->contact_no = $request->input('contact_no');
                $staff->barangay = $request->input('barangay');
                $staff->municipality = $request->input('municipality');
                $staff->city = $request->input('city');
                $staff->province = $request->input('province');
                // $staff->position = $request->input('position');
                $staff->zipcode = $request->input('zipcode');
                // $staff->department_id = $request->input('department_id');
                $staff->user_id = $request->input('profile_id');
            } else {
            }
            //$users->password =Hash::make($request['upassword']);
            $staff->save();
            $users->save();
            return response()->json([
                'success' => 'staff updated successfully'
            ]);
        }
    }
    public function updatepasword(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'current' => [
                'required', function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        return $fail(__('The current password is incorrect'));
                    }
                },
                'min:8',
                'max:30'
            ],
            'up_password' => 'required|min:8',
            'up_confirm_password' => 'required|min:8|same:up_password',
            // 'up_position' => 'required|max:255',
            // 'up_department_id' => 'required|max:255',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {

            $users = User::find($id);
            if (!empty($request->up_password)) {
                $users->password = Hash::make($request['up_password']);
            } else {
            }
            $users->save();
            // $staff->save();
            return response()->json([
                'success' => 'account updated successfully'
            ]);
        }
    }
    public function changeProfilePic(Request $request)
    {
        $path = 'user/';
        $file = $request->file('approver-profile_pic');
        $new_name = 'UIMG_' . date('Ymd') . uniqid() . '.jpg';

        $upload = $file->move(public_path($path), $new_name);

        if (!$upload) {
            return response()->json(['status' => 0, 'msg' => 'something went wrong, upload new picture failed']);
        } else {
            $oldPicture = User::find(Auth::user()->id)->getAttributes()['picture'];
            if ($oldPicture != '') {
                if (\file_exists(public_path($path . $oldPicture))) {
                    \unlink(public_path($path . $oldPicture));
                }
            }

            $update = User::find(Auth::user()->id)->update(['picture' => $new_name]);
            if (!$upload) {
                return response()->json(['status' => 0, 'msg' => 'something went wrong,updating picture in db failed']);
            } else {
                return response()->json(['status' => 1, 'msg' => 'your profile picture has been updated succesfully']);
            }
        }
    }
    public function purchase_request()
    {
        $id = Auth::user()->id;
        $user = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->select(
                'staff.id',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'staff.sex',
                'staff.barangay',
                'staff.municipality',
                'staff.city',
                'staff.province',
                'staff.zipcode',
                'users.position',
                'staff.contact_no',
                'departments.Dept_name',
                'departments.id',
                'buildings.Building_name',
                'buildings.id'
            )
            ->where('staff.id', '=', $id)
            ->first();
        $userr = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                'departments.Dept_name',
                'departments.id',
            )
            ->where('staff.id', '=', $id)
            ->first();
        // $rand = rand(10, 10000);
        // $generatePR = 'PR-' . date("Y-md") . '-' . $rand;
        $latestPR = PurchaseRequest::orderBy('created_at', 'DESC')->first();
        if ($latestPR == null) {
            $generatePR = 'PR-' . date("Y-md") . '-' . str_pad(0 + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $generatePR = 'PR-' . date("Y-md") . '-' . str_pad($latestPR->id + 1, 4, "0", STR_PAD_LEFT);
        }
        $purchaserequest = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->whereNotIn('purchase_request_histories.action', ['Request Denied', 'Approved', 'Request on Hold'])
            ->where('user_id', '=', $id)->get();
        $deny_pr = DeniedRequest::join('purchase_requests', 'denied_requests.pr_no', '=', 'purchase_requests.pr_no')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            ->where('purchase_requests.user_id', '=', $id)->get();
        $hold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.id')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Request on Hold')
            ->where('staff.id', '=', $id)
            // ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get();
        $items = Item::select('items.item_desc')->get()->toArray();
        $approved_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Approved')
            ->where('user_id', '=', $id)->get();
        return view('manage_purchase_request.purchase_request', compact('approved_pr', 'items', 'hold', 'deny_pr', 'generatePR', 'user', 'userr', 'purchaserequest'));
    }
    public function addrequisition(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'type_of_req' => 'required|max:255',
            'purpose' => 'required|max:255',
            // 'beggining' => 'required|max:255',
            // 'ending' => 'required|max:255',
            'quantity' => 'required|max:255',
            'unit' => 'required|max:255',
            'item_desc' => 'required|max:255',
            'pr_no' => 'unique:purchase_requests'

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $name = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'ASSD Manager')
                ->get()->first();
            // dd($approver->email);
            $item = $request->input('item_desc');
            $distinct_item = Item::select('id')
                ->whereIn('item_desc', $item)
                ->get()->toArray();
            // for ($i = 0; $i < count($item); $i++) {
            //     $encode1 = $item;
            //     $encode2 = $distinct_item;
            // }
            // $array = array_values(array_diff_key($encode2, $encode1));
            // dd($distinct_item);
            for ($i = 0; $i < count($item); $i++) {
                $data = [
                    'item_desc' => ucwords($item[$i]),
                    'created_at' => now()
                ];
                // dd($data);
                DB::table('items')->upsert($data, 'item_desc');
            }

            $data = [
                'subject' => 'New Purchase Request',
                'body' => $request->input('pr_no'),
                'from' => $email,
                'name' => $name
            ];
            $pr_no = $request->input('pr_no');
            // $purpose = $request->input('purpose');
            $beggining = $request->input('beggining');
            $ending = $request->input('ending');
            $quantity = $request->input('quantity');
            $unit = $request->input('unit');
            $item_desc = $request->input('item_desc');
            for ($i = 0; $i < count($item_desc); $i++) {
                $datasave = [
                    'pr_no' => $pr_no,
                    'item_desc' => $item_desc[$i],
                    'beggining' => $beggining[$i],
                    'ending' => $ending[$i],
                    'quantity' => $quantity[$i],
                    'unit' => $unit[$i],
                ];
                // $pr_item-> save($datasave);
                DB::table('purchase_request_items')->insert($datasave);
            }
            $requisition = new PurchaseRequest;
            $requisition->pr_no = $request->input('pr_no');
            $requisition->type = $request->input('type_of_req');
            $requisition->purpose = $request->input('purpose');
            $requisition->remarks = 'pending';
            $requisition->department_id = $request->input('department');
            $requisition->user_id = Auth::user()->id;
            $prh = new PurchaseRequestHistory;
            $prh->pr_no = $request->input('pr_no');
            $prh->action = 'New Purchase Request';
            $canvass = new Canvass;
            $canvass->pr_no = $request->input('pr_no');
            Mail::to($sendto->email)->send(new GmailNotification($data));
            $requisition->save();
            $prh->save();
            $canvass->save();

            return response()->json([
                'success' => 'Requisition added successfully'
            ]);
        }
    }
    public function view_purchase_request($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('users', 'purchase_requests.user_id', '=', 'users.id')
            // ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->select(
                '*',
                'purchase_requests.created_at',
                'purchase_requests.updated_at'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->orderBy('purchase_request_items.item_desc', 'asc')
            // ->where('canvass_items.selected','=','1')
            ->get()
            ->toArray();
        $pr_infos = PurchaseRequest::join('users', 'purchase_requests.user_id', '=', 'users.id')
            ->select(
                'purchase_requests.id',
                'purchase_requests.pr_no',
                'purchase_requests.type',
                'purchase_requests.purpose',
                'purchase_requests.remarks',
                'purchase_requests.user_id',
                'purchase_requests.department_id',
                'purchase_requests.supp_docs_id',
                'purchase_requests.w_PO',
                'purchase_requests.created_at',
                'purchase_requests.updated_at',
                'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('items', 'canvass_items.item_id', '=', 'items.id')
            ->join('suppliers', 'items.supplier_id', '=', 'suppliers.id')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            ->get()
            ->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $outputss = [];
        foreach ($pr_infoss as $dataaa) {
            $outputss[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $check = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.sel',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.sel', '=', null)
            ->get()->toArray();
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        //dd($output);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_purchase_requestt', ['user_email' => $user_email, 'check' => $check, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'outputss' => $outputss]);
    }
    public function new_pr()
    {
        $pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'New Purchase Request')
            ->get();
        $canvassing = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'For Canvassing')
            ->get();
        $deny = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                '*',
                'purchase_request_histories.updated_at'
            )
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            ->get();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        // dd($deny);
        return view('manage_purchase_request.new_purchase_request', compact('canvassing', 'deny', 'pr', 'user'));
    }
    public function view_new_pr($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                '*',
                'purchase_request_items.id'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();

        $check = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.sel',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.sel', '=', null)
            ->get()->toArray();
        $chk_item = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.chk_item',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.chk_item', '=', null)
            ->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)
            ->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        // dd($outputs);
        $check_item = Item::join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
            ->select('items.item_desc', 'items.id')
            ->groupBy('items.item_desc', 'items.id')
            ->get()
            ->toArray();
        $item_outputs = [];
        foreach ($check_item as $dataa) {
            $item_outputs[] = $dataa;
        }
        $deny_pr = DeniedRequest::where('denied_requests.pr_no', '=', $pr_no)->get()->toArray();
        $deny = [];
        foreach ($deny_pr as $dataa) {
            $deny[] = $dataa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->select(
                'purchase_request_items.item_desc'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $item_check = Item::join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
            ->select(
                'items.item_desc'
            )
            ->whereIn('items.item_desc',  $pr_info)
            ->groupby('items.item_desc')
            ->get()
            ->toArray();

        // dd($item_check);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_new_purchase_request', ['item_check' => $item_check, 'user_email' => $user_email, 'chk_item' => $chk_item, 'check' => $check, 'deny' => $deny, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'item_outputs' => $item_outputs]);
    }
    public function update_new_pr(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Procurement Officer')
                ->get()->first();
            $reqemail = $request->input('email');
            // dd($sendto);
            $prh = PurchaseRequestHistory::where('pr_no', '=', $pr_no)->first();
            $prh->pr_no = $request->input('pr_no');
            $prh->action = 'For Canvassing';
            $prh->isVerified = null;

            $deny = DeniedRequest::where('pr_no', '=', $pr_no)->first();
            if (!empty($deny)) {
                $deny->delete();
                $pr = PurchaseRequest::where('pr_no', '=', $pr_no)->first();
                $pr->remarks = 'pending';
                $pr->update();
            }
            $pri = $request->input('verify');
            $uid = Auth::user()->id;
            $checkitem = '1';
            // dd($pri);

            $req_item = PurchaseRequestItem::select('item_desc')
                ->whereIn('id', $pri)->get()->toArray();
            // dd($req_item);
            $item_idd = Item::select('id')->whereIn('item_desc', $req_item)
                ->get()->toArray();

            $c = collect($item_idd);
            $thestring = $c->implode('id', ',');

            // dd($pri);
            // $integerIDs = array_map('intval', json_decode($thestring));
            $integerIDs = explode(',', $thestring);
            // dd($integerIDs);
            // DB::table('purchase_request_items')->find($pri)->update($item_idd, 'item_id');
            for ($i = 0; $i < count($pri); $i++) {
                $datasave = [
                    'item_id' => $integerIDs[$i],
                    'checkitemby' => Auth::user()->id,
                    'chk_item' => '1',
                    'updated_at' => now()
                ];
                // dd($datasave);
                DB::table('purchase_request_items')->where('id', $pri[$i])->update($datasave);
            }

            // DB::table('purchase_request_items')->whereIn('id', $pri)->update($item_idd, 'item_id');


            $data = [
                'subject' => 'PR is ready for Canvass',
                'body' => $request->input('pr_no'),
                'from' => $email,
                'name' => $role->fname . '' . $role->lname,
            ];
            Mail::to($sendto->email)->send(new GmailNotification($data));
            Mail::to($reqemail)->send(new Requestor($data));
            $prh->update();
            $item = $request->input('item_id');
            $pr = PurchaseRequestItem::select('pr_no')->where('pr_no', '=', $pr_no)->get()->toArray();
            DB::table('purchase_request_items')->whereIn('id', $pr)->update(['item_id' => $item], true);
            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function deny_new_pr(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            // dd($pr_no);
            $email = Auth::user()->email;
            $name = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $data = [
                'subject' => 'Your request has been denied.',
                'body' => $request->input('pr_no'),
                'from' => $email,
                'name' => $name

            ];
            try {

                $email = $request->input('email');
                $prh = PurchaseRequestHistory::where('pr_no', '=', $pr_no)->first();
                $prh->pr_no = $request->input('pr_no');
                $prh->action = 'Request Denied';
                $prh->isVerified = Auth::user()->id;
                $pr = PurchaseRequest::where('pr_no', '=', $pr_no)->first();
                $pr->remarks = 'Request Denied';
                $dr = new DeniedRequest;
                $dr->pr_no = $request->input('pr_no');
                $dr->deny_message = $request->input('deny_message');
                Mail::to($email)->send(new DenyPR($data));
                $prh->update();
                $pr->update();
                $dr->save();
                return response()->json([
                    'success' => 'Requisition added successfully'
                ]);
            } catch (Exception $e) {
                return response()->json(['Sorry! Please try again latter']);
            }

            // $item = $request->input('item_id');
            // $pr = PurchaseRequestItem::select('pr_no')->where('pr_no', '=', $pr_no)->get()->toArray();
            // DB::table('purchase_request_items')->whereIn('id', $pr)->update(['item_id' => $item], true);

            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function pr_for_verification()
    {
        $pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verifying')
            ->get();
        $verified_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verified')
            ->get();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $approved_prr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            // ->where('purchase_request_histories.action', '=', 'Approved')
            ->whereIn('purchase_request_histories.action',  ['Verified', 'Checked', 'Approved'])
            ->get();
        return view('manage_purchase_request.pr_for_verification', compact('pr', 'user', 'verified_pr', 'approved_prr'));
    }
    public function view_pr_for_verification($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                '*',
                'purchase_request_items.id'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $check = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.sel',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.chk_item', '=', '1')
            ->where('purchase_request_items.sel', '=', null)
            ->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $canvass_info = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'canvass_items.id',
                // 'items.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->orderBy('items.item_desc', 'desc')

            ->get()
            ->toArray();
        $canvass_output = [];
        foreach ($canvass_info as $dataa) {
            $canvass_output[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->select(
                'purchase_request_items.id'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get()->toArray();
        $outputss = [];
        foreach ($pr_infoss as $dataaa) {
            $outputss[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();

        // dd($check);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_for_verification', ['user_email' => $user_email, 'check' => $check, 'user' => $user, 'isverifyBy' => $isverifyBy, 'output' => $output, 'outputs' => $outputs, 'outputss' => $outputss, 'canvass_output' => $canvass_output]);
    }
    public function update_pr_for_verification($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $check = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.sel',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.sel', '=', null)
            ->get()->toArray();
        $canvass_info = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'canvass_items.id',
                'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $canvass_output = [];
        foreach ($canvass_info as $dataa) {
            $canvass_output[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'canvass_items.id',
                'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            ->get()->toArray();
        $outputss = [];
        foreach ($pr_infoss as $dataaa) {
            $outputss[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();

        dd($canvass_output);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.update_pr_for_verification', ['user_email' => $user_email, 'check' => $check, 'user' => $user, 'isverifyBy' => $isverifyBy, 'output' => $output, 'outputs' => $outputs, 'canvass_output' => $canvass_output, 'outputss' => $outputss]);
    }
    public function verify_pr(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $itemm = Item::join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
                ->join('canvass_items', 'supplier_items.id', '=', 'canvass_items.supplier_items_id')
                ->select('item_desc')
                ->where('canvass_items.id', '=', $request->input('checkbox_canvass'))
                ->get()->first();
            $check_item = json_encode($itemm->item_desc);
            $req_item = $request->input('ver_item');
            $sel_item = json_encode($req_item);
            // dd($sel_item);
            if ($check_item == $sel_item) {
                $item_id = $request->input('item_id');

                $item = PurchaseRequestItem::find($item_id);
                $item->sel = '1';
                $item->save();


                $verify_item = $request->input('checkbox_canvass');
                $selected = '1';
                $quan = $request->input('quantity');
                // dd($prh);
                for ($i = 0; $i < count($verify_item); $i++) {
                    $canvass_item = CanvassItem::find($verify_item[$i]);
                    $canvass_item->selected = $selected;
                    $canvass_item->quantity = $quan;
                    $canvass_item->update();
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'error' => 'Item Description doesnt Match',
                ]);
            }
            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function verify_submit(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Finance Head')
                ->get()->first();
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            // dd($reqemail);
            $data = [
                'subject' => 'Supplier has been Selected',
                'body' => $pr_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $prh = PurchaseRequestHistory::where('pr_no', '=', $pr_no)->first();
            // $prh->pr_no = $request->input('pr_no');
            $prh->action = 'Verified';
            $prh->isVerified = Auth::user()->id;
            $prh->dateVerified = now();
            $prh->update();
            Mail::to($sendto->email)->send(new GmailNotification($data));
            Mail::to($reqemail->email)->send(new Requestor($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function update_verified_pr(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $pr_infoss = Canvass::join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
                ->select('canvass_items.id')
                ->where('canvasses.pr_no', '=', $pr_no)
                ->where('canvass_items.selected', '=', '1')
                ->get()->toArray();

            DB::table('canvass_items')->whereIn('id', $pr_infoss)->update(['selected' => '1']);


            $verify_item = $request->input('update_canvass');
            $quan = $request->input('quan');
            $selected = '1';
            // dd($verify_item);
            for ($i = 0; $i < count($verify_item); $i++) {
                $update_canvass_item = CanvassItem::find($verify_item[$i]);
                $update_canvass_item->selected = $selected;
                $update_canvass_item->quantity = $quan[$i];
                $update_canvass_item->update();
            }

            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function delete_verified_item($id)
    {
        $dvi = CanvassItem::find($id);
        $dvi->selected = null;
        $dvi->update();
        return response()->json([
            'success' => 'account deleted successfully'
        ]);
    }
    public function update_quantity(Request $request, $pr_no, $id)
    {
        $quan = $request->input('quantity');
        // dd($id);
        $up_quan = CanvassItem::find($id);
        $up_quan->quantity = $quan;
        $up_quan->update();


        return response()->json([
            'success' => 'updated successfully'
        ]);
    }
    public function checkverifyitem(Request $request, $pr_no, $id)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|max:255',
            // 'up_brand' => 'required|max:255',
            // 'up_unit' => 'required|max:255',
            // 'up_price' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $itemm = Item::select('item_desc')
                ->where('id', '=', $request->input('item_id'))
                ->get()->first();
            $r1 = json_encode($itemm->item_desc);
            $req_item = $request->req_item;
            $r2 = json_encode($req_item);
            // $result = array_diff_key($item, $req_item);
            // dd($r2);
            if ($r1 == $r2) {
                $item = PurchaseRequestItem::find($id);
                $item->item_id = $request->input('item_id');
                $item->checkitemby = Auth::user()->id;
                $item->chk_item = '1';
                // $item->brand = $request->input('up_brand');
                // $item->unit = $request->input('up_unit');
                // $item->price = $request->input('up_price');
                // if (!empty($request->supplier_id)) {
                //     $item->supplier_id = $request->input('up_supplier_id');
                // }

                $item->save();
            } else {
                return response()->json([
                    'status' => 400,
                    'error' => 'Item Description not Match',
                ]);
            }

            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function denyitem(Request $request, $pr_no, $id)
    {
        // dd($request->input('item_id'));
        $validator = Validator::make($request->all(), [
            // 'item_id' => 'required|max:255',
            // 'up_brand' => 'required|max:255',
            // 'up_unit' => 'required|max:255',
            // 'up_price' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $item = PurchaseRequestItem::find($id);
            $item->item_id = '0';
            $item->sel = '0';
            $item->chk_item = '0';
            $item->checkitemby = Auth::user()->id;
            // $item->brand = $request->input('up_brand');
            // $item->unit = $request->input('up_unit');
            // $item->price = $request->input('up_price');
            // if (!empty($request->supplier_id)) {
            //     $item->supplier_id = $request->input('up_supplier_id');
            // }

            $item->save();
            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function pr_for_approval()
    {
        $pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Checked')
            ->get();
        $approved_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->whereIn('purchase_request_histories.action', ['Approved', 'Verified'])
            ->get();
        // dd($approved_pr);
        return view('manage_purchase_request.pr_for_approval', compact('pr', 'approved_pr'));
    }
    public function view_pr_for_approval(Request $request, $pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $check = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.sel',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.sel', '=', null)
            ->get()->toArray();
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                'canvass_items.quantity',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                // 'purchase_request_items.item_desc',
                'supplier_items.offered_price',
                'suppliers.business_name',
                // 'purchase_request_items.sel',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->where('purchase_request_items.sel', '!=', '0')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckfund', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($canvassed_item);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_for_approval', ['user_email' => $user_email, 'check' => $check, 'isapprovedBy' => $isapprovedBy, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function approve_pr(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Chief Executive Officer')
                ->get()->first();
            $sendto2 = User::where('position', '=', 'Procurement Officer')
                ->get()->first();
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            // dd($pr_no);
            $prh = PurchaseRequestHistory::where('pr_no', '=', $pr_no)->first();
            // $prh->pr_no = $request->input('pr_no');
            // $prh->action = 'Approved';
            if ($request->input('position') == "Chief Executive Officer") {
                $data = [
                    'subject' => 'Purchase Request has been Approved!',
                    'body' => $pr_no,
                    'from' => $email,
                    'name' => $role->fname . ' ' . $role->lname,
                ];
                $prh->action = 'Approved';
                $prh->isApproved2 = $request->input('id');
                $prh->dateApproved2 = now();
                $pr = PurchaseRequest::where('pr_no', '=', $pr_no)->first();
                $pr->remarks = 'Approved';
                Mail::to($sendto2->email)->send(new GmailNotification($data));
                Mail::to($reqemail->email)->send(new Requestor($data));
            } else {
                $data = [
                    'subject' => 'PR has been approved by Corporate Treasurer',
                    'body' => $pr_no,
                    'from' => $email,
                    'name' => $role->fname . ' ' . $role->lname,
                ];
                $prh->isApproved = $request->input('id');
                $prh->dateApproved = now();
                $pr = PurchaseRequest::where('pr_no', '=', $pr_no)->first();
                $pr->remarks = 'Approving';
                Mail::to($sendto->email)->send(new GmailNotification($data));
                Mail::to($reqemail->email)->send(new Requestor($data));
            }
            $prh->update();
            $pr->update();

            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function view_approved_pr(Request $request, $pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $check = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.sel',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.sel', '=', null)
            ->get()->toArray();
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                'canvass_items.quantity',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                // 'purchase_request_items.item_desc',
                'supplier_items.offered_price',
                'suppliers.business_name',
                // 'purchase_request_items.sel',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->where('purchase_request_items.sel', '!=', '0')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckFund', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $isapproved2 = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved2', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapproved2By = [];
        foreach ($isapproved2 as $dataa) {
            $isapproved2By[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($outputs);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_approved_pr', ['user_email' => $user_email, 'check' => $check, 'isapproved2By' => $isapproved2By, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy, 'isapprovedBy' => $isapprovedBy, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function po_for_approval()
    {
        $purchase_order = PurchaseOrder::select('*')->get();
        $po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Prepared')
            ->get();
        $verified_po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Verified')
            ->get();
        $approving = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approving')
            ->get();
        $approved_po = PurchaseOrder::select('*')
            ->whereIn('purchase_orders.status',  ['Approved', 'Ordered'])
            ->get();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $poforapproval = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Verified')
            ->get()
            ->count();
        // dd($approved_po);
        return view('manage_purchase_order.prepared_po', compact('poforapproval', 'purchase_order', 'po', 'verified_po', 'user', 'approved_po', 'approving'));
    }
    public function view_prepared_po(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                'purchase_orders.paymentTerm',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'purchase_request_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()->toArray();

        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        // dd($po_output);
        return view('manage_purchase_order.view_prepared_po', ['user' => $user, 'output' => $output, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'po_output' => $po_output]);
    }
    public function verify_po(Request $request, $po_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'paymentTerm' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            // dd($pr_no);
            // $po = PurchaseOrder::find($po_no);
            $id = $request->input('id');
            $pt = $request->input('paymentTerm');
            $status = 'Verified';
            // $po->save();
            DB::table('purchase_orders')->where('po_no', $po_no)->update(['verifiedBy' => $id, 'status' => $status, 'paymentTerm' => $pt], true);

            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function view_verified_po(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->join('orders', 'supplier_items.supplier_id', '=', 'orders.supplier_id')
            ->select(
                // '*',
                'purchase_requests.user_id',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                'orders.payment_term',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                'canvass_items.supplier_items_id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.supplier_id',
                'supplier_items.offered_price',
                'suppliers.business_name',
                'suppliers.business_add',
                'suppliers.email',
                'suppliers.contact_no',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $Collection = collect($pr_infoss);
        $groupCollection = $Collection->groupBy('business_name')->values();
        $gg = collect($groupCollection)->all();
        return view('manage_purchase_order.view_verified_po', ['gg' => $gg, 'user' => $user, 'output' => $output, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'po_output' => $po_output]);
    }
    public function approve_po(Request $request, $po_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Procurement Officer')
                ->get()->first();
            $pr_no = $request->input('pr_no');
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            $po_no = $request->input('po_no');
            $data = [
                'subject' => 'Purchase Order has been Approved',
                'body' => $pr_no,
                'po' => $po_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $id = $request->input('id');
            $status = 'Approved';
            $approveddate = now();
            DB::table('purchase_orders')->where('po_no', $po_no)->update(['approvedBy' => $id, 'approvedDate' => $approveddate, 'status' => $status], true);
            Mail::to($sendto->email)->send(new GeneratedPO($data));
            Mail::to($reqemail->email)->send(new Requestor_PO($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function view_approved_po(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $approve = PurchaseOrder::join('staff', 'purchase_orders.approvedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approvedBy = [];
        foreach ($approve as $dataa) {
            $approvedBy[] = $dataa;
        }
        $approve2 = PurchaseOrder::join('staff', 'purchase_orders.approved2By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved2By = [];
        foreach ($approve2 as $dataa) {
            $approved2By[] = $dataa;
        }
        $approve3 = PurchaseOrder::join('staff', 'purchase_orders.approved3By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved3By = [];
        foreach ($approve3 as $dataa) {
            $approved3By[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            // ->join('orders', 'supplier_items.supplier_id', '=', 'orders.supplier_id')
            ->select(
                // '*',
                'purchase_requests.user_id',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                // 'orders.payment_term',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                'canvass_items.supplier_items_id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.supplier_id',
                'supplier_items.offered_price',
                'suppliers.business_name',
                'suppliers.business_add',
                'suppliers.email',
                'suppliers.contact_no',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $pb = Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.description', '=', 'Processor')->get()->toArray();
        // dd($pb);
        $Collection = collect($pr_infoss);
        $groupCollection = $Collection->groupBy('business_name')->values();
        $gg = collect($groupCollection)->all();
        return view('manage_purchase_order.purchase_order_form', ['gg' => $gg, 'pb' => $pb, 'user' => $user, 'output' => $output, 'approved3By' => $approved3By, 'approved2By' => $approved2By, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'approvedBy' => $approvedBy, 'po_output' => $po_output]);
    }
    public function approveCT_po(Request $request, $po_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $id = $request->input('id');
            $status = 'Approving';
            DB::table('purchase_orders')->where('po_no', $po_no)->update(['approved2By' => $id, 'status' => $status], true);

            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function approveCEO_po(Request $request, $po_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $id = $request->input('id');
            $status = 'Approved';
            DB::table('purchase_orders')->where('po_no', $po_no)->update(['approved3By' => $id, 'status' => $status], true);

            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function view_track_pr($pr_no)
    {
        $ischeckitem = PurchaseRequestItem::join('items', 'purchase_request_items.item_id', '=', 'items.id')
            ->select(
                'purchase_request_items.item_id',
                'purchase_request_items.updated_at'
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckitemBy = [];
        foreach ($ischeckitem as $dataa) {
            $ischeckitemBy[] = $dataa;
        }
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckFund', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $isapproved2 = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved2', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapproved2By = [];
        foreach ($isapproved2 as $dataa) {
            $isapproved2By[] = $dataa;
        }
        $canvass = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $canvassed = [];
        foreach ($canvass as $dataa) {
            $canvassed[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $pr = PurchaseRequest::join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                '*',
                'purchase_requests.created_at'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();

        $id = Auth::user()->id;
        $user = PurchaseRequest::join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get()
            ->toArray();

        $processor = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('roles.name', '=', 'Processor')
            ->get()->toArray();
        $reqdenied = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->select(
                'purchase_request_histories.action',
                'purchase_requests.updated_at',
                'purchase_request_histories.isVerified',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            // ->where('purchase_request_histories.isVerified','=',$id)
            ->get()->toArray();
        $vv = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')

            ->select(
                'purchase_request_histories.isVerified',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            // ->where('purchase_request_histories.isVerified','=',$id)
            ->get()->toArray();
        $deniedby = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'purchase_request_histories.updated_at',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            ->where('purchase_request_histories.isVerified', '=', $vv)
            ->get()->toArray();
        $checkitemby = PurchaseRequestItem::join('items', 'purchase_request_items.item_id', '=', 'items.id')
            ->join('staff', 'purchase_request_items.checkitemby', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)->get()->toArray();
        $hold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Request on Hold')
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get();
        $ishold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isHold', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_app = PurchaseRequest::select('pr_no')->where('user_id', '=', Auth::user()->id)->get()->toArray();
        $prp = PurchaseRequest::select(
            '*',
            'purchase_requests.created_at'
        )
            ->whereIn('pr_no', $pr_app)
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($user);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.track_pr', ['prp' => $prp, 'pr_app' => $pr_app, 'ishold' => $ishold, 'hold' => $hold, 'checkitemby' => $checkitemby, 'deniedby' => $deniedby, 'reqdenied' => $reqdenied, 'ischeckitemBy' => $ischeckitemBy, 'isapprovedBy' => $isapprovedBy, 'processor' => $processor, 'user' => $user, 'canvassed' => $canvassed, 'pr' => $pr, 'isapproved2By' => $isapproved2By, 'isapprovedBy' => $isapprovedBy, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy]);
    }
    public function purchase_order()
    {
        $id = Auth::user()->id;
        $purchaseorder = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select('*')
            ->where('user_id', '=', $id)->get();
        // dd($purchaseorder);
        return view('manage_purchase_order.purchase_order', compact('purchaseorder'));
    }
    public function view_track_po($pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $po = [];
        foreach ($pr_info as $data) {
            $po[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $approve = PurchaseOrder::join('staff', 'purchase_orders.approvedBy', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approvedBy = [];
        foreach ($approve as $dataa) {
            $approvedBy[] = $dataa;
        }
        $approve2 = PurchaseOrder::join('staff', 'purchase_orders.approved2By', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved2By = [];
        foreach ($approve2 as $dataa) {
            $approved2By[] = $dataa;
        }
        $approve3 = PurchaseOrder::join('staff', 'purchase_orders.approved3By', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved3By = [];
        foreach ($approve3 as $dataa) {
            $approved3By[] = $dataa;
        }
        $orderdate = PurchaseOrder::where('purchase_orders.pr_no', '=', $pr_no)
        ->where('purchase_orders.orderDate', '!=', null)->get()->toArray();
        $orderDate = [];
        foreach ($orderdate as $dataa) {
            $orderDate[] = $dataa;
        }

        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $po_app = PurchaseRequest::join('purchase_orders', 'purchase_requests.pr_no', '=', 'purchase_orders.pr_no')
            ->select('purchase_orders.pr_no')->where('purchase_requests.user_id', '=', Auth::user()->id)->get()->toArray();
        $pop = PurchaseRequest::join('purchase_orders', 'purchase_requests.pr_no', '=', 'purchase_orders.pr_no')
            ->select(
                '*',
                'purchase_requests.created_at'
            )
            ->whereIn('purchase_orders.pr_no', $po_app)
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($approved2By);
        return view('manage_purchase_order.track_po', ['orderDate' => $orderDate,'pop' => $pop, 'user' => $user, 'po' => $po, 'approved3By' => $approved3By, 'approved2By' => $approved2By, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'approvedBy' => $approvedBy]);
    }
    public function to_received()
    {
        $id = Auth::user()->id;
        $to_received = Delivery::join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'deliveries.order_no',
                'del_fwds.created_at',

            )
            ->where('purchase_requests.user_id', '=', $id)
            ->whereIn('del_fwds.status',  ['With Delivery No.', 'For Approval', 'Approved'])
            // ->where('del_fwds.status', '=', 'Approved')
            ->groupby(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'deliveries.order_no',
                'del_fwds.created_at',
            )
            ->get();
        $forwarded = Delivery::join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            // ->join('forwardeds', 'deliveries.delivery_no', '=', 'forwardeds.delivery_no')
            ->select(
                // 'forwardeds.forward_no',
                'del_fwds.id',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'del_fwds.created_at',
                // 'forwardeds.staff_id',
            )
            ->where('purchase_requests.user_id', '=', $id)
            ->whereIn('del_fwds.status',  ['With Delivery No.', 'For Approval', 'Approved'])
            ->groupby(
                // 'forwardeds.forward_no',
                'del_fwds.id',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'del_fwds.created_at',
                // 'forwardeds.staff_id',
            )
            ->get();
        $fw_approved = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->select(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
            )
            ->where('del_fwds.status', '=', 'Approved')
            ->get()
            ->unique();
        $item_received = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.reqreceivedDate',
                'deliveries.order_no',

            )
            ->where('del_fwds.status', '=', 'Item Received')
            ->where('purchase_requests.user_id', '=', $id)
            ->groupby(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.reqreceivedDate',
                'deliveries.order_no',
            )
            ->get();
        $reported = Report::join('deliveries', 'reports.delivery_no', '=', 'deliveries.delivery_no')->select('*')
            ->where('staff_id', '=', $id)
            ->get();
        // dd($fw_approved);
        return view('manage_forwarded.forwarded_app', compact('reported', 'item_received', 'to_received', 'forwarded', 'fw_approved'));
    }
    public function view_po_to_received(Request $request, $dln, $po_no)
    {
        // dd($dln);
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->select('*')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'del_fwds.status',
                'del_fwds.delivery_no',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'purchase_requests.user_id',
                // 'purchase_requests.created_at',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'deliveries.order_no',
                'del_fwds.forwardedDate',
                'deliveries.supplier_id',
                'users.position',
                'del_fwds.created_at',
            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            // ->where('forwardeds.staff_id', '=', $staff_id)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = User::join('del_fwds', 'users.id', '=', 'del_fwds.isApproved')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $rand = rand(10, 10000);
        $generatedReportNo = 'RN' . date("Y-md") . $rand;
        $report = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->where('reports.staff_id', '=', $id)
            ->get()
            ->toArray();
        $isforwarded = DelFwd::select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->where('del_fwds.isForwarded', '=', '1')
            ->get()->toArray();
        // dd($delivery);
        return view('manage_forwarded.view_forwardedd', ['isforwarded' => $isforwarded, 'report' => $report, 'generatedReportNo' => $generatedReportNo, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'forwarded' => $forwarded, 'delivery' => $delivery]);
    }
    public function view_denied_pr(Request $request, $pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $deny_pr = DeniedRequest::where('denied_requests.pr_no', '=', $pr_no)->get()->toArray();
        $deny = [];
        foreach ($deny_pr as $dataa) {
            $deny[] = $dataa;
        }
        $check = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                'purchase_request_items.sel',
                'purchase_requests.pr_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.sel', '=', null)
            ->get()->toArray();
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        return view('manage_purchase_request.view_denied_pr', ['user_email' => $user_email, 'check' => $check, 'deny_pr' => $deny_pr, 'output' => $output, 'outputs' => $outputs]);
    }
    public function to_transmit()
    {
        $forwarded = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->select(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.forwardedDate',
                'deliveries.order_no'
            )
            ->groupby(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.forwardedDate',
                'deliveries.order_no'

            )
            ->where('del_fwds.status', '=', 'For Approval')
            ->get();
        $fw_approved = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->select(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.approvedDate',
                'deliveries.order_no'
            )
            ->groupby(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.approvedDate',
                'deliveries.order_no'
            )
            ->whereIn('del_fwds.status', ['Approved', 'Item Received'])
            ->get();

        // dd($fw_approved);
        return view('manage_forwarded.forwarded', compact('forwarded', 'fw_approved'));
    }
    public function view_forwarded(Request $request, $dln, $po_no)
    {
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->select('*', 'forwarded_items.id')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'del_fwds.status',
                'del_fwds.delivery_no',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'purchase_requests.user_id',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'del_fwds.forwardedDate',
                'purchase_requests.created_at',
                'deliveries.order_no',
                'deliveries.supplier_id',
                'users.position',
            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('deliveries.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            // ->where('forwardeds.staff_id', '=', $staff_id)
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = User::join('del_fwds', 'users.id', '=', 'del_fwds.isApproved')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $isforwarded = User::join('del_fwds', 'users.id', '=', 'del_fwds.isForwarded')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $rand = rand(10, 10000);
        $generatedReportNo = 'RN' . date("Y-md") . $rand;
        $report = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->where('reports.staff_id', '=', $id)
            ->get()
            ->toArray();
        // dd($delivery);
        return view('manage_forwarded.view_forwarded', ['isforwarded' => $isforwarded, 'forwarded' => $forwarded, 'report' => $report, 'generatedReportNo' => $generatedReportNo, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'delivery' => $delivery]);
    }
    public function view_forwardedd(Request $request, $dln, $po_no)
    {
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->select('*', 'forwarded_items.id')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'del_fwds.status',
                'del_fwds.delivery_no',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'purchase_requests.user_id',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'del_fwds.forwardedDate',
                'purchase_requests.created_at',
                'deliveries.order_no',
                'deliveries.supplier_id',
                'users.position',
            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            // ->where('forwardeds.staff_id', '=', $staff_id)
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = User::join('del_fwds', 'users.id', '=', 'del_fwds.isApproved')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $isforwarded = User::join('del_fwds', 'users.id', '=', 'del_fwds.isForwarded')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $rand = rand(10, 10000);
        $generatedReportNo = 'RN' . date("Y-md") . $rand;
        $report = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->where('reports.staff_id', '=', $id)
            ->get()
            ->toArray();
        // dd($delivery);
        return view('manage_forwarded.view_forwarded', ['isforwarded' => $isforwarded, 'forwarded' => $forwarded, 'report' => $report, 'generatedReportNo' => $generatedReportNo, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'delivery' => $delivery]);
    }
    public function view_forwardeddd(Request $request, $dln, $po_no)
    {
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->select('*', 'forwarded_items.id')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'del_fwds.status',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'purchase_requests.created_at',
                'staff.user_id',
                'del_fwds.delivery_no',
                'del_fwds.forwardedDate',
                'deliveries.order_no',
                'deliveries.supplier_id',
                'users.position',

            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            // ->where('forwardeds.staff_id', '=', $staff_id)
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = User::join('del_fwds', 'users.id', '=', 'del_fwds.isApproved')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $isforwarded = User::join('del_fwds', 'users.id', '=', 'del_fwds.isForwarded')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $rand = rand(10, 10000);
        $generatedReportNo = 'RN' . date("Y-md") . $rand;
        $report = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->where('reports.staff_id', '=', $id)
            ->get()
            ->toArray();
        // dd($isApproved);
        return view('manage_forwarded.view_forwarded_done', ['isforwarded' => $isforwarded, 'forwarded' => $forwarded, 'report' => $report, 'generatedReportNo' => $generatedReportNo, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'delivery' => $delivery]);
    }
    public function approved_forward(Request $request, $dln, $po_no, $staff_id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'date' => 'required|max:255',
            // 'deliverby' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            // $sendto = User::where('position', '=', 'Procurement Officer')
            //     ->get()->first();
            // $pr_no = $request->input('pr_no');
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->join('purchase_orders', 'purchase_requests.pr_no', '=', 'purchase_orders.pr_no')
                ->select(
                    'users.email',
                    'purchase_requests.pr_no'
                )
                ->where('purchase_orders.po_no', '=', $po_no)->get()->first();
            $po_no = $request->input('po_no');
            $data = [
                'subject' => 'Delivery Approved - ',
                'body' => $reqemail->pr_no,
                'fwd_no' => null,
                'po' => $po_no,
                'dln' => $dln,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $user = $request->input('user');
            $fwn = $request->input('fwn');
            DB::table('del_fwds')->where('delivery_no', $dln)->update(['status' => 'Approved', 'isApproved' => $user, 'approvedDate' => now(), 'forward_no' => $fwn]);
            // Mail::to($sendto->email)->send(new GeneratedPO($data));
            Mail::to($reqemail->email)->send(new DeliveryNo($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);


            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function received_item(Request $request, $dln, $staff_id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'date' => 'required|max:255',
            // 'deliverby' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Procurement Officer')
                ->get()->first();
            // $pr_no = $request->input('pr_no');
            $pr = PurchaseOrder::select(
                'pr_no'
            )
                ->where('po_no', '=', $request->input('po_no'))->get()->first();
            $reqemail = Auth::user()->email;

            $po_noo = $request->input('po_no');
            $data = [
                'subject' => 'Item Received - ',
                'body' => $pr->pr_no,
                'fwd_no' => null,
                'po' => $po_noo,
                'dln' => $dln,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $user = $request->input('staff_id');
            DB::table('del_fwds')->where('delivery_no', $dln)->update(['status' => 'Item Received', 'isReqReceived' => $user, 'reqreceivedDate' => now()]);
            Mail::to($sendto->email)->send(new DeliveryNo($data));
            Mail::to($reqemail)->send(new DeliveryNo($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function track_po_to_received(Request $request, $dln, $po_no)
    {
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->select('*')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                'del_fwds.status',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'departments.Dept_name',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'deliveries.delivery_no'

            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('deliveries.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')

            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            // ->where('forwardeds.staff_id', '=', $staff_id)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = DelFwd::select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $isApprovedBy = DelFwd::join('staff', 'del_fwds.isApproved', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $del = DelFwd::where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $delby = Staff::join('users','staff.id','=','users.id')
        ->where('users.position','=','Procurement Officer')
        ->get()->toArray();
        // dd($delivery);
        return view('manage_forwarded.track_forwarded', ['delby' => $delby,'del' => $del,'isApprovedBy' => $isApprovedBy, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'forwarded' => $forwarded, 'delivery' => $delivery]);
    }
    public function report_item(Request $request, $dln)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'FID' => ['unique:report_items'],
        ], [
            'FID.unique' => 'Already Reported'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Procurement Officer')
                ->get()->first();
            $data = [
                'subject' => 'Report Item - ',
                'body' => $request->input('rn'),
                // 'po' => $po_noo,
                // 'dln' => $dln,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $id = Auth::user()->id;
            $report = new Report;
            $report->Report_no = $request->input('rn');
            $report->delivery_no = $request->input('dln');
            $report->staff_id = $id;
            $report->save();

            for ($i = 0; $i < count($request->report_chk); $i++) {
                $datasave = [
                    'report_no' => $request->input('rn'),
                    'FID' => $request->input('report_chk')[$i],
                    'item_status' => $request->input('report_message')[$i],
                    'quantity' => $request->input('report_quantity')[$i],
                ];
                // dd($datasave);
                DB::table('report_items')->insert($datasave);
            }

            Mail::to($sendto->email)->send(new ReportItemm($data));

            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function view_reported_item(Request $request, $rn)
    {
        $id = Auth::user()->id;
        $reported = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->join('forwarded_items', 'report_items.FID', '=', 'forwarded_items.id')
            ->join('staff', 'reports.staff_id', '=', 'staff.id')
            ->select('*', 'reports.created_at')
            ->where('reports.Report_no', '=', $rn)
            ->where('reports.staff_id', '=', $id)
            ->get()
            // ->unique()
            ->toArray();
        $rand = rand(10, 10000);
        $generatedReportNo = 'RN' . date("Y-md") . $rand;
        // dd($reported);
        return view('manage_forwarded.view_reported_item', ['generatedReportNo' => $generatedReportNo, 'reported' => $reported]);
    }
}
