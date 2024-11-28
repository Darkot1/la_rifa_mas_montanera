import{T as p,k as y,e as l,b as t,u as o,w as s,F as v,o as u,Z as x,a as i,f as b,n as h,g as r,j as d,i as _}from"./app-BIChde90.js";import{A as k}from"./AuthenticationCard-HUYeRzOq.js";import{_ as w}from"./AuthenticationCardLogo-B8T-C0fS.js";import{_ as V}from"./PrimaryButton-8akujhFA.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const E={key:0,class:"mb-4 font-medium text-sm text-green-600"},B={class:"mt-4 flex items-center justify-between"},F={__name:"VerifyEmail",props:{status:String},setup(f){const m=f,n=p({}),c=()=>{n.post(route("verification.send"))},g=y(()=>m.status==="verification-link-sent");return(a,e)=>(u(),l(v,null,[t(o(x),{title:"Email Verification"}),t(k,null,{logo:s(()=>[t(w)]),default:s(()=>[e[3]||(e[3]=i("div",{class:"mb-4 text-sm text-gray-600"}," Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another. ",-1)),g.value?(u(),l("div",E," A new verification link has been sent to the email address you provided in your profile settings. ")):b("",!0),i("form",{onSubmit:_(c,["prevent"])},[i("div",B,[t(V,{class:h({"opacity-25":o(n).processing}),disabled:o(n).processing},{default:s(()=>e[0]||(e[0]=[r(" Resend Verification Email ")])),_:1},8,["class","disabled"]),i("div",null,[t(o(d),{href:a.route("profile.show"),class:"underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"},{default:s(()=>e[1]||(e[1]=[r(" Edit Profile")])),_:1},8,["href"]),t(o(d),{href:a.route("logout"),method:"post",as:"button",class:"underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2"},{default:s(()=>e[2]||(e[2]=[r(" Log Out ")])),_:1},8,["href"])])])],32)]),_:1})],64))}};export{F as default};
