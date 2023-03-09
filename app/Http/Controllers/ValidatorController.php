<?php

namespace App\Http\Controllers;

use App\Mail\CheckedPR;
use App\Mail\DeliveryNo;
use App\Mail\DenyPR;
use App\Mail\GeneratedPO;
use App\Mail\GmailNotification;
use App\Mail\ReportItemm;
use App\Mail\Requestor;
use App\Mail\Requestor_PO;
use App\Mail\VerifiedPO;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Building;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Canvass;
use App\Models\PurchaseOrder;
use App\Models\CanvassItem;
use App\Models\PurchaseRequestItem;
use App\Models\PurchaseRequestHistory;
use App\Models\Delivery;
use App\Models\DeniedRequest;
use App\Models\Forwarded;
use App\Models\DelFwd;
use App\Models\Report;
use App\Models\ReportItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Auth\Events\Verified;

class ValidatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:Validator']);
    }
    public function index()
    {
        $id = Auth::user()->id;
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
                'purchase_requests.user_id',
            )
            ->where('purchase_requests.user_id', '=', $id)
            ->whereIn('del_fwds.status',  ['With Delivery No.', 'For Approval', 'Approved'])
            // ->where('del_fwds.status', '=', 'Approved')
            ->groupby(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
            )
            ->get()
            ->count();
        $checkfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->where('purchase_request_histories.action', '=', 'Verified')->get()
            ->count();
        $po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Prepared')
            ->get()
            ->count();
        $userr = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.zipcode',
            )
            ->where('staff.id', '=', $id)
            ->first();
        return view('validator.dashboard', compact('userr', 'po', 'checkfund', 'toreceived', 'purchaseorder', 'my_pr'));
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
        $file = $request->file('validator-profile_pic');
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
            $approver = User::select('email')->where('position', '=', 'ASSD Manager')->get()->first();
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
            Mail::to($approver->email)->send(new GmailNotification($data));
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
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)
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
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($outputs);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_purchase_requesttt', ['user_email' => $user_email, 'output' => $output, 'outputs' => $outputs]);
    }
    public function pr_check_fund()
    {
        $pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verified')->get();
        $checked_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            // ->where('purchase_request_histories.action', '=', 'Checked')
            ->whereIn('purchase_request_histories.action',  ['Checked', 'Approved'])
            ->get();
        $hold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Request on Hold')
            ->get();

        // dd($hold);
        return view('manage_purchase_request.pr_check_fund', compact('hold', 'pr', 'checked_pr'));
    }
    public function view_pr_check_fund($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->orderBy('purchase_request_items.item_desc', 'asc')
            ->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
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
        $perSupplier = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                DB::raw('sum(DISTINCT canvass_items.quantity*supplier_items.offered_price)as count'),
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            ->groupBy(
                'suppliers.business_name',
            )
            ->get()
            ->toArray();


        $perSupp = [];
        foreach ($perSupplier as $dataaa) {
            $perSupp[] = $dataaa;
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
        $hold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_histories.action', '=', 'Request on Hold')
            ->get();
        $deny_pr = DeniedRequest::where('denied_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($ischeckfund);
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($hold);
        return view('manage_purchase_request.view_pr_check_fund', ['user_email' => $user_email, 'deny_pr' => $deny_pr, 'hold' => $hold, 'ischeckfundBy' => $ischeckfundBy, 'perSupp' => $perSupp, 'isverifyBy' => $isverifyBy, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function check_pr(Request $request, $pr_no)
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
            $sendto = User::where('position', '=', 'Corporate Treasurer')
                ->get()->first();
            // $sendto2 = User::where('position', '=', 'Chief Executive Officer')
            //     ->get()->first();
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            // dd($reqemail);
            $data = [
                'subject' => 'Your Request has been approved by Finance Head.',
                'body' => $pr_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $prh = PurchaseRequestHistory::where('pr_no', '=', $pr_no)->first();
            // $prh->pr_no = $request->input('pr_no');
            $prh->action = 'Checked';
            $prh->isCheckfund = $request->input('id');
            $prh->dateCheckfund = now();
            $prh->isHold = null;
            $prh->DateHold = null;
            $prh->update();

            $deny = DeniedRequest::where('pr_no', '=', $pr_no)->first();
            if (!empty($deny)) {
                $deny->delete();
                $pr = PurchaseRequest::where('pr_no', '=', $pr_no)->first();
                $pr->remarks = 'pending';
                $pr->update();
            }
            Mail::to($sendto->email)->send(new GmailNotification($data));
            // Mail::to($sendto2->email)->send(new GmailNotification($data));
            Mail::to($reqemail->email)->send(new Requestor($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function view_pr_checked_fund($pr_no)
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
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        $perSupplier = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                DB::raw('sum(purchase_request_items.quantity*supplier_items.offered_price)as count'),
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            ->groupBy(
                'suppliers.business_name',
            )
            ->get()
            ->toArray();


        $perSupp = [];
        foreach ($perSupplier as $dataaa) {
            $perSupp[] = $dataaa;
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
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckfund', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        // dd($ischeckfundBy);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_checked_fund', ['perSupp' => $perSupp, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function po_for_approval()
    {
        $purchase_order = PurchaseOrder::select('*')->get();
        $po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Prepared')
            ->get();
        $verified_po = PurchaseOrder::select('*')
            ->whereIn('purchase_orders.status', ['Verified', 'Approved', 'Ordered'])
            ->get();
        $approving = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approving')
            ->get();
        $approved_po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approved')
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
        // dd($verified_po);
        return view('manage_purchase_order.prepared_po', compact('purchase_order', 'po', 'verified_po', 'user', 'approved_po', 'approving'));
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
            ->select(
                // '*',
                'purchase_requests.user_id',
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
        return view('manage_purchase_order.view_prepared_po', ['gg' => $gg, 'user' => $user, 'output' => $output, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'po_output' => $po_output]);
    }
    public function verify_po(Request $request, $po_no)
    {
        $validator = Validator::make($request->all(), [
            'paymentTerm' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $count = count($request->input('paymentTerm'));
            $count2 = count($request->input('supplier_id'));
            // dd($count);
            if ($count != $count2) {
                session()->flash('error', 'The payment term is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'The payment term is required.'
                ], 400);
            } else {
                // dd($request->all());

                $email = Auth::user()->email;
                $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                    ->select('staff.fname', 'staff.lname')
                    ->where('users.id', '=', Auth::user()->id)
                    ->get()->first();
                $sendto = User::where('position', '=', 'ASSD Manager')
                    ->get()->first();
                $pr_no = $request->input('pr_no');
                $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                    ->select('users.email')
                    ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
                $po_no = $request->input('po_no');
                $data = [
                    'subject' => 'Purchase Order has been Verified',
                    'body' => $pr_no,
                    'po' => $po_no,
                    'from' => $email,
                    'name' => $role->fname . ' ' . $role->lname,
                ];
                $id = $request->input('id');
                $bid = $request->input('b_id');
                $pt = $request->input('paymentTerm');
                $si = $request->input('supplier_id');
                // dd($si);
                $status = 'Verified';
                $verifieddate = now();
                // $po->save();
                for ($i = 0; $i < count($pt); $i++) {
                    $datasavee = [
                        'po_no' => $po_no,
                        'supplier_id' => $si[$i],
                        'payment_term' => $pt[$i],
                        'pr_no' => $pr_no,
                    ];
                    DB::table('purchase_orders')->where('po_no', $po_no)->update(['verifiedBy' => $id, 'verifiedDate' => $verifieddate, 'status' => $status], true);
                    DB::table('orders')->insert($datasavee);
                }
                Mail::to($sendto->email)->send(new GeneratedPO($data));
                Mail::to($reqemail->email)->send(new Requestor_PO($data));
                return response()->json([
                    'success' => 'Supplier has been Selected'
                ]);
            }
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
        // dd($gg);
        return view('manage_purchase_order.view_verified_po', ['gg' => $gg, 'user' => $user, 'output' => $output, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'po_output' => $po_output]);
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
        $Collection = collect($pr_infoss);
        $groupCollection = $Collection->groupBy('business_name')->values();
        $gg = collect($groupCollection)->all();
        // dd($pb);
        return view('manage_purchase_order.purchase_order_form', ['gg' => $gg, 'pb' => $pb, 'user' => $user, 'output' => $output, 'approved3By' => $approved3By, 'approved2By' => $approved2By, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'approvedBy' => $approvedBy, 'po_output' => $po_output]);
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
        // dd($prp);
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
        return view('manage_purchase_order.track_po', ['orderDate' => $orderDate, 'pop' => $pop, 'user' => $user, 'po' => $po, 'approved3By' => $approved3By, 'approved2By' => $approved2By, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'approvedBy' => $approvedBy]);
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
        return view('manage_forwarded.forwarded', compact('reported', 'item_received', 'to_received', 'forwarded', 'fw_approved'));
    }
    public function view_po_to_received(Request $request, $dln, $po_no, $user_id)
    {
        // dd($);
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
        // dd($delivery);
        return view('manage_forwarded.view_forwarded', ['forwarded' => $forwarded, 'report' => $report, 'generatedReportNo' => $generatedReportNo, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'delivery' => $delivery]);
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
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        return view('manage_purchase_request.view_denied_pr', ['user_email' => $user_email, 'deny_pr' => $deny_pr, 'output' => $output, 'outputs' => $outputs]);
    }
    public function received_item(Request $request, $dln, $po_no,)
    {
        // dd($dln);
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
                'po' => $po_noo,
                'fwd_no' => null,
                'dln' => $dln,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            try {
                $user = $request->input('staff_id');
                DB::table('del_fwds')->where('delivery_no', $dln)->update(['status' => 'Item Received', 'isReqReceived' => $user, 'reqreceivedDate' => now()]);
                Mail::to($sendto->email)->send(new DeliveryNo($data));
                Mail::to($reqemail)->send(new DeliveryNo($data));
                return response()->json([
                    'success' => 'Supplier has been Selected'
                ]);
            } catch (Exception $e) {
                return response()->json(['Sorry! Please try again latter']);
            }
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
        $delby = Staff::join('users', 'staff.id', '=', 'users.id')
            ->where('users.position', '=', 'Procurement Officer')
            ->get()->toArray();
        // dd($delivery);
        return view('manage_forwarded.track_forwarded', ['delby' => $delby, 'del' => $del, 'isApprovedBy' => $isApprovedBy, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'forwarded' => $forwarded, 'delivery' => $delivery]);
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
            ->toArray();
        // dd($reported);
        return view('manage_forwarded.view_reported_item', ['reported' => $reported]);
    }
    public function hold_new_pr(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'deny_message' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            // dd($pr_no);

            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            // $sendto = User::where('position', '=', 'Finance Head')
            //     ->get()->first();
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            // dd($reqemail);
            $data = [
                'subject' => 'Request on Hold',
                'body' => $pr_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            try {
                $prh = PurchaseRequestHistory::where('pr_no', '=', $pr_no)->first();
                $prh->pr_no = $request->input('pr_no');
                $prh->action = 'Request on Hold';
                $prh->isHold = Auth::user()->id;
                $prh->dateHold = now();
                $prh->update();
                $dr = new DeniedRequest;
                $dr->pr_no = $request->input('pr_no');;
                $dr->deny_message = $request->input('deny_message');;
                $dr->save();
                // Mail::to($sendto->email)->send(new SelectSupplier($data));
                Mail::to($reqemail->email)->send(new DenyPR($data));
                return response()->json([
                    'success' => 'Supplier has been Selected'
                ]);
            } catch (Exception $e) {
                return response()->json(['Sorry! Please try again latter']);
            }
            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
}
